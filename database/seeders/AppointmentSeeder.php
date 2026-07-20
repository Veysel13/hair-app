<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Business;
use App\Models\CustomerProfile;
use App\Models\Review;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $customers  = User::where('role', 'customer')->get();
        $businesses = Business::with('staff', 'services')->get();

        $statuses = ['completed', 'completed', 'completed', 'cancelled', 'no_show'];
        $futureStatuses = ['confirmed', 'confirmed', 'pending'];

        foreach ($businesses as $business) {
            $staff    = $business->staff;
            $services = $business->services;

            if ($staff->isEmpty() || $services->isEmpty()) continue;

            // ── 30 Geçmiş Randevu ────────────────
            for ($i = 0; $i < 30; $i++) {
                $customer   = $customers->random();
                $staffMember= $staff->random();
                $service    = $services->random();
                $status     = $statuses[array_rand($statuses)];
                $date       = now()->subDays(rand(1, 120))->format('Y-m-d');
                $startHour  = rand(9, 17);
                $startTime  = sprintf('%02d:00:00', $startHour);
                $endTime    = sprintf('%02d:%02d:00', $startHour + intdiv($service->duration_minutes, 60), $service->duration_minutes % 60);

                $appointment = Appointment::create([
                    'business_id'    => $business->id,
                    'customer_id'    => $customer->id,
                    'staff_id'       => $staffMember->id,
                    'appointment_date' => $date,
                    'start_time'     => $startTime,
                    'end_time'       => $endTime,
                    'status'         => $status,
                    'total_duration' => $service->duration_minutes,
                    'total_price'    => $service->price,
                    'deposit_paid'   => $status === 'completed',
                    'source'         => 'app',
                ]);

                AppointmentService::create([
                    'appointment_id'  => $appointment->id,
                    'service_id'      => $service->id,
                    'staff_id'        => $staffMember->id,
                    'duration_minutes'=> $service->duration_minutes,
                    'price'           => $service->price,
                    'sort_order'      => 1,
                ]);

                // CRM profil güncelle
                $profile = CustomerProfile::firstOrCreate(
                    ['business_id' => $business->id, 'customer_id' => $customer->id],
                    ['total_visits' => 0, 'total_spent' => 0, 'last_visit_at' => null]
                );
                
                $profile->total_visits += 1;
                $profile->total_spent += $service->price;
                if ($status === 'completed') {
                    $profile->last_visit_at = $date;
                }
                $profile->save();

                // Tamamlanan randevulara yorum ekle
                if ($status === 'completed' && rand(0, 10) > 3) {
                    $this->addReview($appointment, $business->id, $customer->id, $staffMember->id);
                }
            }

            // ── 10 Gelecek Randevu ────────────────
            for ($i = 0; $i < 10; $i++) {
                $customer    = $customers->random();
                $staffMember = $staff->random();
                $service     = $services->random();
                $status      = $futureStatuses[array_rand($futureStatuses)];
                $date        = now()->addDays(rand(1, 30))->format('Y-m-d');
                $startHour   = rand(9, 17);
                $startTime   = sprintf('%02d:00:00', $startHour);
                $endTime     = sprintf('%02d:%02d:00', $startHour + intdiv($service->duration_minutes, 60), $service->duration_minutes % 60);

                $appointment = Appointment::create([
                    'business_id'     => $business->id,
                    'customer_id'     => $customer->id,
                    'staff_id'        => $staffMember->id,
                    'appointment_date'=> $date,
                    'start_time'      => $startTime,
                    'end_time'        => $endTime,
                    'status'          => $status,
                    'total_duration'  => $service->duration_minutes,
                    'total_price'     => $service->price,
                    'deposit_paid'    => rand(0, 1) === 1,
                    'source'          => collect(['app', 'phone', 'walk_in'])->random(),
                ]);

                AppointmentService::create([
                    'appointment_id'  => $appointment->id,
                    'service_id'      => $service->id,
                    'staff_id'        => $staffMember->id,
                    'duration_minutes'=> $service->duration_minutes,
                    'price'           => $service->price,
                    'sort_order'      => 1,
                ]);
            }
        }
    }

    private function addReview(Appointment $appointment, int $businessId, int $customerId, int $staffId): void
    {
        $comments = [
            'Harika bir deneyimdi, kesinlikle tavsiye ederim!',
            'Personel çok ilgili ve profesyoneldi.',
            'Sonuçtan çok memnun kaldım, tekrar geleceğim.',
            'Randevu saatine sadık kaldılar, harika hizmet.',
            'Ortam çok şık ve temizdi.',
            'Beklediğimden çok daha iyi oldu!',
            'Fiyat/performans oranı çok iyi.',
            null, // bazı yorumlar sadece puan
        ];

        Review::create([
            'appointment_id'  => $appointment->id,
            'business_id'     => $businessId,
            'customer_id'     => $customerId,
            'staff_id'        => $staffId,
            'rating'          => rand(3, 5),
            'staff_rating'    => rand(3, 5),
            'comment'         => $comments[array_rand($comments)],
            'is_visible'      => true,
        ]);
    }
}
