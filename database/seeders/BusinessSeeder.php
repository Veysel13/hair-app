<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessHour;
use App\Models\User;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        $owners = User::where('role', 'business_owner')->get();

        // ── 1. Lux Hair Salon ────────────────────
        $lux = Business::create([
            'owner_id'             => $owners[0]->id,
            'name'                 => 'Lux Hair Salon',
            'slug'                 => 'lux-hair-salon',
            'description'          => 'İstanbul\'un kalbinde, saç tasarımında mükemmeliyeti arayan kadın ve erkeklere özel premium kuaför deneyimi.',
            'phone'                => '02121234567',
            'email'                => 'info@luxhair.com',
            'website'              => 'https://luxhair.com',
            'address'              => 'Bağdat Caddesi No:142/A',
            'city'                 => 'İstanbul',
            'district'             => 'Kadıköy',
            'latitude'             => 40.9731,
            'longitude'            => 29.0789,
            'category'             => 'hair_salon',
            'is_active'            => true,
            'avg_rating'           => 4.8,
            'total_reviews'        => 127,
            'deposit_required'     => true,
            'deposit_percentage'   => 30,
            'cancellation_hours'   => 24,
        ]);
        $this->seedHours($lux, '09:00', '20:00', [0]); // Pazar kapalı

        // ── 2. Demir Barber ──────────────────────
        $barber = Business::create([
            'owner_id'             => $owners[1]->id,
            'name'                 => 'Demir Barber',
            'slug'                 => 'demir-barber',
            'description'          => 'Geleneksel berber sanatını modern tekniklerle buluşturan, erkeklere özel premium bakım deneyimi.',
            'phone'                => '02129876543',
            'email'                => 'info@demirbarber.com',
            'address'              => 'Nişantaşı Mah. Valikonağı Cd. No:55',
            'city'                 => 'İstanbul',
            'district'             => 'Şişli',
            'latitude'             => 41.0491,
            'longitude'            => 28.9943,
            'category'             => 'barber',
            'is_active'            => true,
            'avg_rating'           => 4.6,
            'total_reviews'        => 89,
            'deposit_required'     => false,
            'cancellation_hours'   => 12,
        ]);
        $this->seedHours($barber, '08:00', '21:00', []); // Her gün açık

        // ── 3. Nail Topia ────────────────────────
        $nail = Business::create([
            'owner_id'             => $owners[2]->id,
            'name'                 => 'Nail Topia',
            'slug'                 => 'nail-topia',
            'description'          => 'Tırnak sanatında sınırları zorlayan yaratıcı tasarımlar. El ve ayak bakımında İstanbul\'un en iyi deneyimi.',
            'phone'                => '02165432109',
            'email'                => 'info@nailtopia.com',
            'address'              => 'Cevizlik Mah. Millet Cd. No:78/B',
            'city'                 => 'İstanbul',
            'district'             => 'Bakırköy',
            'latitude'             => 40.9823,
            'longitude'            => 28.8715,
            'category'             => 'nail_salon',
            'is_active'            => true,
            'avg_rating'           => 4.9,
            'total_reviews'        => 203,
            'deposit_required'     => true,
            'deposit_percentage'   => 25,
            'cancellation_hours'   => 24,
        ]);
        $this->seedHours($nail, '10:00', '19:00', [0, 1]); // Pazar + Pazartesi kapalı
    }

    private function seedHours(Business $business, string $open, string $close, array $closedDays): void
    {
        for ($day = 0; $day <= 6; $day++) {
            BusinessHour::create([
                'business_id'  => $business->id,
                'day_of_week'  => $day,
                'is_open'      => ! in_array($day, $closedDays),
                'open_time'    => in_array($day, $closedDays) ? null : $open,
                'close_time'   => in_array($day, $closedDays) ? null : $close,
            ]);
        }
    }
}
