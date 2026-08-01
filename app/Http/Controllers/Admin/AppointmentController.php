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
            'customer_type'      => 'required|in:existing,new',
            'customer_id'        => 'required_if:customer_type,existing|nullable|exists:users,id',
            'new_customer_name'  => 'required_if:customer_type,new|nullable|string|max:100',
            'new_customer_phone' => 'required_if:customer_type,new|nullable|string|max:20',
            'staff_id'           => 'required|exists:staff,id',
            'appointment_date'   => 'required|date|after_or_equal:today',
            'start_time'         => 'required',
            'service_ids'        => 'required|array|min:1',
            'service_ids.*'      => 'exists:services,id',
            'notes'              => 'nullable|string|max:500',
            'source'             => 'in:app,phone,walk_in',
        ]);

        if ($data['customer_type'] === 'new') {
            // Telefon numarasına göre var olan müşteri var mı kontrol et veya oluştur
            $cleanPhone = preg_replace('/\s+/', '', $data['new_customer_phone']);
            $customer = User::where('role', 'customer')
                ->where('phone', $cleanPhone)
                ->first();

            if (!$customer) {
                $customer = User::create([
                    'name'     => $data['new_customer_name'],
                    'phone'    => $cleanPhone,
                    'email'    => $cleanPhone . '@salonim.com.tr',
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(12)),
                    'role'     => 'customer',
                ]);
            }
            $customerId = $customer->id;
        } else {
            $customerId = $data['customer_id'];
        }

        $services     = Service::whereIn('id', $data['service_ids'])->get();
        $totalDuration= $services->sum('duration_minutes');
        $totalPrice   = $services->sum('price');

        $startTime = $data['start_time'];
        $endTime   = date('H:i:s', strtotime($startTime) + $totalDuration * 60);

        $appointment = Appointment::create([
            'business_id'      => $business->id,
            'customer_id'      => $customerId,
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
        $appointment->load([
            'customer', 
            'staff', 
            'services.service', 
            'payments', 
            'review', 
            'campaignUsage.campaign',
            'treatmentRecords.service',
            'treatmentRecords.staff'
        ]);

        $customerProfile = \App\Models\CustomerProfile::firstOrCreate([
            'business_id' => $appointment->business_id,
            'customer_id' => $appointment->customer_id,
        ]);

        return view('admin.appointments.show', compact('appointment', 'customerProfile'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate(['status' => 'required|in:confirmed,in_progress,completed,cancelled,no_show']);
        $appointment->update(['status' => $request->status]);

        if ($request->status === 'cancelled') {
            $appointment->update(['cancelled_at' => now(), 'cancellation_reason' => $request->reason]);
        }

        // Müşteri profili istatistiklerini otomatik güncelle
        $profile = \App\Models\CustomerProfile::firstOrCreate([
            'business_id' => $appointment->business_id,
            'customer_id' => $appointment->customer_id,
        ]);
        
        $completedAppts = Appointment::where('business_id', $appointment->business_id)
            ->where('customer_id', $appointment->customer_id)
            ->where('status', 'completed');

        $profile->update([
            'total_visits' => $completedAppts->count(),
            'total_spent'  => $completedAppts->sum('total_price'),
            'last_visit_at'=> $completedAppts->max('appointment_date') ?? $profile->last_visit_at,
        ]);

        return back()->with('success', 'Randevu durumu güncellendi.');
    }

    public function checkout(Request $request, Appointment $appointment)
    {
        $request->validate([
            'payment_method'  => 'required|in:cash,card,split,online',
            'cash_amount'     => 'nullable|numeric|min:0',
            'card_amount'     => 'nullable|numeric|min:0',
            'tip_amount'      => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'internal_notes'  => 'nullable|string',
        ]);

        $paymentMethod  = $request->payment_method;
        $originalPrice  = (float) $appointment->total_price;
        $discountAmount = (float) ($request->discount_amount ?? 0);
        
        // Net tahsil edilecek tutar (Hizmet Toplamı - İndirim)
        $netPrice = max(0, $originalPrice - $discountAmount);

        $cashAmount = 0.0;
        $cardAmount = 0.0;
        $tipAmount  = (float) ($request->tip_amount ?? 0);

        if ($paymentMethod === 'cash') {
            $cashAmount = $netPrice;
        } elseif ($paymentMethod === 'card') {
            $cardAmount = $netPrice;
        } elseif ($paymentMethod === 'split') {
            $cashAmount = (float) ($request->cash_amount ?? 0);
            $cardAmount = (float) ($request->card_amount ?? 0);
        } else {
            $cardAmount = $netPrice;
        }

        $appointment->update([
            'status'          => 'completed',
            'payment_method'  => $paymentMethod,
            'cash_amount'     => $cashAmount,
            'card_amount'     => $cardAmount,
            'tip_amount'      => $tipAmount,
            'discount_amount' => $discountAmount,
            'completed_at'    => now(),
            'internal_notes'  => $request->internal_notes ? $request->internal_notes : $appointment->internal_notes,
        ]);

        // Ödeme kaydı oluştur/güncelle (Tahsil edilen net tutar + bahşiş)
        \App\Models\Payment::updateOrCreate([
            'appointment_id' => $appointment->id,
        ], [
            'business_id'    => $appointment->business_id,
            'customer_id'    => $appointment->customer_id,
            'amount'         => $netPrice + $tipAmount,
            'type'           => 'full',
            'method'         => $paymentMethod === 'card' ? 'credit_card' : ($paymentMethod === 'split' ? 'cash' : $paymentMethod),
            'status'         => 'completed',
            'paid_at'        => now(),
        ]);

        // Müşteri profili istatistiklerini güncelle (Gerçekleşen harcama net tutardır)
        $profile = \App\Models\CustomerProfile::firstOrCreate([
            'business_id' => $appointment->business_id,
            'customer_id' => $appointment->customer_id,
        ]);
        
        $completedAppts = Appointment::where('business_id', $appointment->business_id)
            ->where('customer_id', $appointment->customer_id)
            ->where('status', 'completed');

        $profile->update([
            'total_visits' => $completedAppts->count(),
            'total_spent'  => $completedAppts->get()->sum(function($apt) {
                return max(0, $apt->total_price - ($apt->discount_amount ?? 0));
            }),
            'last_visit_at'=> $completedAppts->max('appointment_date') ?? $profile->last_visit_at,
        ]);

        return back()->with('success', 'Adisyon başarıyla kapatıldı ve kasaya işlendi.');
    }
}
