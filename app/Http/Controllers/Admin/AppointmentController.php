<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    private function business(): Business
    {
        $user = Auth::user();
        return $user->isAdmin() ? Business::first() : $user->businesses()->firstOrFail();
    }

    public function index(Request $request)
    {
        $business = $this->business();
        $query    = Appointment::with(['customer', 'staff', 'services.service'])
            ->where('business_id', $business->id);

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('staff_id')) $query->where('staff_id', $request->staff_id);
        if ($request->filled('date'))     $query->where('appointment_date', $request->date);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"));
        }

        $appointments = $query->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->paginate(20);

        $staff = $business->staff()->where('is_active', true)->get();

        return view('admin.appointments.index', compact('appointments', 'staff', 'business'));
    }

    public function create()
    {
        $business = $this->business();
        $staff    = $business->activeStaff;
        $services = $business->activeServices()->with('category')->get();
        $customers = User::where('role', 'customer')->orderBy('name')->get();

        return view('admin.appointments.create', compact('business', 'staff', 'services', 'customers'));
    }

    public function store(Request $request)
    {
        $business = $this->business();

        $data = $request->validate([
            'customer_id'      => 'required|exists:users,id',
            'staff_id'         => 'required|exists:staff,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time'       => 'required',
            'service_ids'      => 'required|array|min:1',
            'service_ids.*'    => 'exists:services,id',
            'notes'            => 'nullable|string|max:500',
            'source'           => 'in:app,phone,walk_in',
        ]);

        $services     = Service::whereIn('id', $data['service_ids'])->get();
        $totalDuration= $services->sum('duration_minutes');
        $totalPrice   = $services->sum('price');

        $startTime = $data['start_time'];
        $endTime   = date('H:i:s', strtotime($startTime) + $totalDuration * 60);

        $appointment = Appointment::create([
            'business_id'      => $business->id,
            'customer_id'      => $data['customer_id'],
            'staff_id'         => $data['staff_id'],
            'appointment_date' => $data['appointment_date'],
            'start_time'       => $startTime,
            'end_time'         => $endTime,
            'status'           => 'confirmed',
            'total_duration'   => $totalDuration,
            'total_price'      => $totalPrice,
            'notes'            => $data['notes'] ?? null,
            'source'           => $data['source'] ?? 'phone',
        ]);

        foreach ($services as $i => $service) {
            $appointment->services()->create([
                'service_id'       => $service->id,
                'staff_id'         => $data['staff_id'],
                'duration_minutes' => $service->duration_minutes,
                'price'            => $service->price,
                'sort_order'       => $i + 1,
            ]);
        }

        return redirect()->route('admin.appointments.show', $appointment)
            ->with('success', 'Randevu başarıyla oluşturuldu.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['customer', 'staff', 'services.service', 'payments', 'review', 'campaignUsage.campaign']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate(['status' => 'required|in:confirmed,in_progress,completed,cancelled,no_show']);
        $appointment->update(['status' => $request->status]);

        if ($request->status === 'cancelled') {
            $appointment->update(['cancelled_at' => now(), 'cancellation_reason' => $request->reason]);
        }

        return back()->with('success', 'Randevu durumu güncellendi.');
    }
}
