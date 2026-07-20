<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Models\StaffWorkingHour;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $businesses = Business::all();

        // ── Lux Hair Salon Personeli ─────────────
        $lux = $businesses->firstWhere('slug', 'lux-hair-salon');
        $luxStaff = [
            ['name' => 'Derya Şimşek',  'title' => 'Baş Stilist',          'display_order' => 1],
            ['name' => 'Cansu Yıldırım','title' => 'Renklendirme Uzmanı',  'display_order' => 2],
            ['name' => 'Tuğba Akar',    'title' => 'Keratin & Bakım',      'display_order' => 3],
            ['name' => 'Mert Güzel',    'title' => 'Erkek Saç Uzmanı',     'display_order' => 4],
        ];
        $this->createStaff($lux, $luxStaff, [0]); // Pazar kapalı

        // ── Demir Barber Personeli ────────────────
        $barber = $businesses->firstWhere('slug', 'demir-barber');
        $barberStaff = [
            ['name' => 'Kadir Demir',   'title' => 'Usta Berber',          'display_order' => 1],
            ['name' => 'Serkan Kaya',   'title' => 'Sakal & Saç Uzmanı',   'display_order' => 2],
            ['name' => 'Ufuk Çelik',    'title' => 'Berber',               'display_order' => 3],
        ];
        $this->createStaff($barber, $barberStaff, []);

        // ── Nail Topia Personeli ──────────────────
        $nail = $businesses->firstWhere('slug', 'nail-topia');
        $nailStaff = [
            ['name' => 'Zehra Koç',     'title' => 'Nail Artist',          'display_order' => 1],
            ['name' => 'Aylin Arslan',  'title' => 'Kalıcı Oje Uzmanı',   'display_order' => 2],
            ['name' => 'Seda Turan',    'title' => 'El & Ayak Bakım',      'display_order' => 3],
            ['name' => 'Filiz Güner',   'title' => 'Protez Tırnak Uzmanı','display_order' => 4],
        ];
        $this->createStaff($nail, $nailStaff, [0, 1]); // Pazar + Pazartesi kapalı
    }

    private function createStaff(Business $business, array $staffData, array $closedDays): void
    {
        foreach ($staffData as $data) {
            $staff = Staff::create(array_merge($data, [
                'business_id' => $business->id,
                'is_active'   => true,
            ]));

            // Çalışma saatleri
            for ($day = 0; $day <= 6; $day++) {
                StaffWorkingHour::create([
                    'staff_id'   => $staff->id,
                    'day_of_week'=> $day,
                    'is_working' => ! in_array($day, $closedDays),
                    'start_time' => in_array($day, $closedDays) ? null : '09:00:00',
                    'end_time'   => in_array($day, $closedDays) ? null : '19:00:00',
                ]);
            }
        }
    }
}
