<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Staff;
use App\Models\StaffWorkingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    private function business(): Business
    {
        $user = Auth::user();
        return $user->isAdmin() ? Business::first() : $user->businesses()->firstOrFail();
    }

    public function index()
    {
        $business = $this->business();
        $staff    = $business->staff()->with('workingHours', 'portfolio')->orderBy('display_order')->get();

        // Personel istatistikleri
        $stats = $staff->map(function ($s) {
            return [
                'id'                => $s->id,
                'total_appointments'=> $s->appointments()->count(),
                'this_month'        => $s->appointments()
                    ->whereMonth('appointment_date', now()->month)
                    ->where('status', 'completed')->count(),
                'avg_rating'        => round($s->reviews()->avg('staff_rating') ?? 0, 1),
            ];
        })->keyBy('id');

        return view('admin.staff.index', compact('business', 'staff', 'stats'));
    }

    public function create()
    {
        $business = $this->business();
        $services = $business->activeServices()->with('category')->get();
        return view('admin.staff.create', compact('business', 'services'));
    }

    public function store(Request $request)
    {
        $business = $this->business();
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'title'           => 'nullable|string|max:100',
            'bio'             => 'nullable|string|max:1000',
            'display_order'   => 'nullable|integer|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'service_ids'     => 'nullable|array',
        ]);

        $staff = $business->staff()->create([
            'name'            => $data['name'],
            'title'           => $data['title'] ?? null,
            'bio'             => $data['bio'] ?? null,
            'display_order'   => $data['display_order'] ?? 0,
            'commission_rate' => $data['commission_rate'] ?? 30.00,
            'is_active'       => true,
        ]);

        // Varsayılan çalışma saatleri (Pazartesi-Cumartesi)
        for ($day = 0; $day <= 6; $day++) {
            StaffWorkingHour::create([
                'staff_id'   => $staff->id,
                'day_of_week'=> $day,
                'is_working' => $day !== 0, // Pazar kapalı
                'start_time' => $day !== 0 ? '09:00:00' : null,
                'end_time'   => $day !== 0 ? '19:00:00' : null,
            ]);
        }

        if (! empty($data['service_ids'])) {
            $staff->services()->attach($data['service_ids']);
        }

        return redirect()->route('admin.staff.index')->with('success', 'Personel eklendi.');
    }

    public function edit(Staff $staff)
    {
        $business = $this->business();
        $services = $business->activeServices()->with('category')->get();
        $staff->load('workingHours', 'services');
        return view('admin.staff.edit', compact('business', 'staff', 'services'));
    }

    public function update(Request $request, Staff $staff)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'title'           => 'nullable|string|max:100',
            'bio'             => 'nullable|string|max:1000',
            'is_active'       => 'boolean',
            'display_order'   => 'nullable|integer|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'service_ids'     => 'nullable|array',
        ]);

        $staff->update($data);
        $staff->services()->sync($data['service_ids'] ?? []);

        return redirect()->route('admin.staff.index')->with('success', 'Personel güncellendi.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Personel silindi.');
    }

    public function commissions(Request $request)
    {
        $business = $this->business();

        if (!$business->enable_commission) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Personel prim sistemi pasiftir. İşletme ayarlarından aktifleştirebilirsiniz.');
        }

        $period = $request->get('period', 'this_month');
        $query = \App\Models\Appointment::where('business_id', $business->id)
            ->where('status', 'completed');

        if ($period === 'this_month') {
            $query->whereMonth('appointment_date', now()->month)
                  ->whereYear('appointment_date', now()->year);
        } elseif ($period === 'last_month') {
            $query->whereMonth('appointment_date', now()->subMonth()->month)
                  ->whereYear('appointment_date', now()->subMonth()->year);
        } elseif ($period === 'this_year') {
            $query->whereYear('appointment_date', now()->year);
        } elseif ($period === 'custom' && $request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('appointment_date', [$request->start_date, $request->end_date]);
        }

        $completedAppointments = $query->with(['services.service', 'staff'])->get();
        $staffList = $business->staff()->get();

        $reportData = $staffList->map(function ($s) use ($completedAppointments) {
            $staffAppts = $completedAppointments->filter(fn($a) => $a->staff_id === $s->id);
            
            $totalRevenue = $staffAppts->sum('total_price');
            $completedCount = $staffAppts->count();
            
            $commissionRate = $s->commission_rate ?? 30.00;
            $earnedCommission = $totalRevenue * ($commissionRate / 100);

            $allServiceNames = [];
            foreach ($staffAppts as $a) {
                foreach ($a->services as $as) {
                    if ($as->service?->name) {
                        $allServiceNames[] = $as->service->name;
                    }
                }
            }
            $serviceCounts = array_count_values($allServiceNames);
            arsort($serviceCounts);
            $topService = !empty($serviceCounts) ? array_key_first($serviceCounts) : '—';

            return [
                'staff'            => $s,
                'completed_count'  => $completedCount,
                'total_revenue'    => $totalRevenue,
                'commission_rate'  => $commissionRate,
                'earned_commission'=> $earnedCommission,
                'top_service'      => $topService,
            ];
        })->sortByDesc('total_revenue');

        $grandTotalRevenue    = $reportData->sum('total_revenue');
        $grandTotalCommission = $reportData->sum('earned_commission');
        $grandTotalAppts      = $reportData->sum('completed_count');
        $topPerformer         = $reportData->first()['staff']->name ?? '—';

        return view('admin.staff.commissions', compact(
            'business',
            'period',
            'reportData',
            'grandTotalRevenue',
            'grandTotalCommission',
            'grandTotalAppts',
            'topPerformer'
        ));
    }
}
