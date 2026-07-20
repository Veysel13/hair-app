<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Campaign;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $businesses = Business::all();

        foreach ($businesses as $business) {
            // Doğum günü kampanyası
            Campaign::create([
                'business_id'       => $business->id,
                'name'              => 'Doğum Günü İndirimi 🎂',
                'description'       => 'Doğum gününüzde tüm hizmetlerde %20 indirim!',
                'type'              => 'birthday',
                'discount_type'     => 'percentage',
                'discount_value'    => 20,
                'starts_at'         => now()->subMonth(),
                'ends_at'           => null,
                'is_active'         => true,
                'max_uses_per_customer' => 1,
            ]);

            // Geri kazanım kampanyası
            Campaign::create([
                'business_id'       => $business->id,
                'name'              => 'Sizi Özledik! 💜',
                'description'       => '3 aydır bizi ziyaret etmediniz. Geri dönün, %15 indirim kazanın!',
                'type'              => 'win_back',
                'discount_type'     => 'percentage',
                'discount_value'    => 15,
                'starts_at'         => now()->subWeeks(2),
                'ends_at'           => now()->addMonths(3),
                'is_active'         => true,
            ]);

            // Salı sabahı flash kampanya
            Campaign::create([
                'business_id'          => $business->id,
                'name'                 => 'Salı Sabahı Avantajı ⚡',
                'description'          => 'Her Salı sabahı 09:00-12:00 arasında %10 indirim!',
                'type'                 => 'flash',
                'discount_type'        => 'percentage',
                'discount_value'       => 10,
                'applicable_days'      => [2], // Salı
                'applicable_time_start'=> '09:00:00',
                'applicable_time_end'  => '12:00:00',
                'starts_at'            => now()->subMonth(),
                'ends_at'              => now()->addYear(),
                'is_active'            => true,
            ]);
        }
    }
}
