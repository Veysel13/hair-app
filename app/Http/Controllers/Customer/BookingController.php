<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\CustomerProfile;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\NewAppointmentNotification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function showcase(string $slug): View
    {
        $business = Business::where('slug', $slug)
            ->with(['hours', 'images', 'activeStaff', 'activeServices.category', 'visibleReviews.customer'])
            ->firstOrFail();

        return view('customer.showcase', compact('business'));
    }

    public function show(Business $business): View
    {
        $business->load(['activeServices.category', 'activeStaff']);
        $services = $business->activeServices;
        $staff = $business->activeStaff;

        return view('customer.booking', compact('business', 'services', 'staff'));
    }

    public function getAvailableSlots(Request $request, Business $business): JsonResponse
    {
        $data = $request->validate([
            'date'        => 'required|date|after_or_equal:today',
            'staff_id'    => 'required|exists:staff,id',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
        ]);

        $date = $data['date'];
        $staffId = $data['staff_id'];
        $serviceIds = $data['service_ids'];

        $staffMember = Staff::with('workingHours')->findOrFail($staffId);
        $dayOfWeek = date('w', strtotime($date));

        // Personelin o günkü çalışma saatleri
        $workingHour = $staffMember->workingHours->firstWhere('day_of_week', $dayOfWeek);
        if (!$workingHour || !$workingHour->is_working || !$workingHour->start_time || !$workingHour->end_time) {
            return response()->json([]);
        }

        // Toplam süre
        $servicesList = Service::whereIn('id', $serviceIds)->get();
        $totalDuration = $servicesList->sum('duration_minutes');

        $startStr = $workingHour->start_time;
        $endStr = $workingHour->end_time;

        // Mevcut randevuları al
        $existingAppointments = Appointment::where('staff_id', $staffId)
            ->where('appointment_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $slots = [];
        $current = strtotime($startStr);
        $end = strtotime($endStr);

        while ($current + ($totalDuration * 60) <= $end) {
            $slotStart = date('H:i:s', $current);
            $slotEnd = date('H:i:s', $current + ($totalDuration * 60));

            // Geçmiş saatleri filtrele (eğer bugünse)
            if ($date === date('Y-m-d') && $current < strtotime(now()->format('H:i:s'))) {
                $current += 15 * 60; // 15 dakikalık adımlarla ilerle
                continue;
            }

            // Çakışma kontrolü
            $hasOverlap = false;
            foreach ($existingAppointments as $appt) {
                $apptStart = $appt->start_time;
                $apptEnd = $appt->end_time;

                if ($slotStart < $apptEnd && $slotEnd > $apptStart) {
                    $hasOverlap = true;
                    break;
                }
            }

            if (!$hasOverlap) {
                $slots[] = date('H:i', $current);
            }

            $current += 15 * 60; // 15 dakikalık adımlarla kaydır
        }

        return response()->json($slots);
    }

    public function store(Request $request, Business $business): RedirectResponse
    {
        $rules = [
            'staff_id'         => 'required|exists:staff,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time'       => 'required',
            'service_ids'      => 'required|array|min:1',
            'service_ids.*'    => 'exists:services,id',
            'notes'            => 'nullable|string|max:500',
        ];

        // Giriş yapmamışsa isim ve telefon zorunlu
        if (!Auth::check()) {
            $rules['name']  = 'required|string|max:100';
            $rules['phone'] = 'required|string|max:20';
        }

        $data = $request->validate($rules);

        // Kullanıcıyı tespit et veya oluştur
        if (Auth::check()) {
            $customer = Auth::user();
        } else {
            // Telefon numarasıyla eşleşen müşteri var mı?
            $customer = User::where('phone', $data['phone'])
                ->where('role', 'customer')
                ->first();

            if (!$customer) {
                // Yoksa otomatik hesap aç
                $customer = User::create([
                    'name'     => $data['name'],
                    'phone'    => $data['phone'],
                    'email'    => $data['phone'] . '@hairapp.com',
                    'password' => Hash::make(Str::random(12)),
                    'role'     => 'customer',
                ]);
            }
        }

        $services = Service::whereIn('id', $data['service_ids'])->get();
        $totalDuration = $services->sum('duration_minutes');
        $totalPrice = $services->sum('price');

        $startTime = $data['start_time'];
        $endTime = date('H:i:s', strtotime($startTime) + $totalDuration * 60);

        // Randevu oluştur
        $appointment = Appointment::create([
            'business_id'      => $business->id,
            'customer_id'      => $customer->id,
            'staff_id'         => $data['staff_id'],
            'appointment_date' => $data['appointment_date'],
            'start_time'       => $startTime,
            'end_time'         => $endTime,
            'status'           => 'pending', // Onay veya ön ödeme bekleniyor
            'total_duration'   => $totalDuration,
            'total_price'      => $totalPrice,
            'notes'            => $data['notes'] ?? null,
            'source'           => 'app',
        ]);

        // Randevu hizmetlerini kaydet
        foreach ($services as $i => $service) {
            $appointment->services()->create([
                'service_id'       => $service->id,
                'staff_id'         => $data['staff_id'],
                'duration_minutes' => $service->duration_minutes,
                'price'            => $service->price,
                'sort_order'       => $i + 1,
            ]);
        }

        // Müşteri CRM profilini güncelle/oluştur
        $profile = CustomerProfile::firstOrCreate(
            ['business_id' => $business->id, 'customer_id' => $customer->id],
            ['total_visits' => 0, 'total_spent' => 0]
        );
        $profile->total_visits += 1;
        $profile->total_spent += $totalPrice;
        $profile->save();

        // Salon sahibine kurum içi bildirim gönder
        if ($business->owner) {
            $business->owner->notify(new NewAppointmentNotification($appointment));
        }

        // Eğer misafir ise otomatik login yapıp paneline yönlendirelim
        if (!Auth::check()) {
            Auth::login($customer);
        }

        return redirect()->route('customer.dashboard')
            ->with('success', 'Randevunuz başarıyla oluşturuldu! Onay bekleniyor.');
    }
}
