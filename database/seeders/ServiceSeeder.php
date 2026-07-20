<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $businesses = Business::all();

        // ── Lux Hair Salon ────────────────────────
        $lux = $businesses->firstWhere('slug', 'lux-hair-salon');
        $luxStaff = $lux->staff;

        $kesimCat = ServiceCategory::create(['business_id' => $lux->id, 'name' => 'Kesim', 'icon' => '✂️', 'color' => '#d946ef', 'sort_order' => 1]);
        $boyaCat  = ServiceCategory::create(['business_id' => $lux->id, 'name' => 'Renklendirme', 'icon' => '🎨', 'color' => '#a855f7', 'sort_order' => 2]);
        $bakimCat = ServiceCategory::create(['business_id' => $lux->id, 'name' => 'Bakım & Şekillendirme', 'icon' => '💆', 'color' => '#6366f1', 'sort_order' => 3]);

        $luxServices = [
            ['cat' => $kesimCat, 'name' => 'Saç Kesimi (Kadın)',    'duration' => 45,  'price' => 350],
            ['cat' => $kesimCat, 'name' => 'Saç Kesimi (Erkek)',    'duration' => 30,  'price' => 200],
            ['cat' => $kesimCat, 'name' => 'Fringe Kesim',           'duration' => 20,  'price' => 150],
            ['cat' => $boyaCat,  'name' => 'Tam Boya',               'duration' => 120, 'price' => 800],
            ['cat' => $boyaCat,  'name' => 'Röfle / Balayage',       'duration' => 180, 'price' => 1200],
            ['cat' => $boyaCat,  'name' => 'Ombre',                  'duration' => 150, 'price' => 950],
            ['cat' => $boyaCat,  'name' => 'Renk Tazele',            'duration' => 90,  'price' => 500],
            ['cat' => $bakimCat, 'name' => 'Keratin Düzleştirme',   'duration' => 180, 'price' => 1500],
            ['cat' => $bakimCat, 'name' => 'Saç Bakım Maskesi',      'duration' => 60,  'price' => 350],
            ['cat' => $bakimCat, 'name' => 'Fön & Şekillendirme',    'duration' => 45,  'price' => 250],
            ['cat' => $bakimCat, 'name' => 'Gelin Saçı',             'duration' => 120, 'price' => 2000],
        ];

        foreach ($luxServices as $s) {
            $service = Service::create([
                'business_id'      => $lux->id,
                'category_id'      => $s['cat']->id,
                'name'             => $s['name'],
                'duration_minutes' => $s['duration'],
                'price'            => $s['price'],
                'is_active'        => true,
            ]);
            $service->staff()->attach($luxStaff->pluck('id'));
        }

        // ── Demir Barber ─────────────────────────
        $barber = $businesses->firstWhere('slug', 'demir-barber');
        $barberStaff = $barber->staff;

        $saçCat   = ServiceCategory::create(['business_id' => $barber->id, 'name' => 'Saç', 'icon' => '💈', 'color' => '#3b82f6', 'sort_order' => 1]);
        $sakalCat = ServiceCategory::create(['business_id' => $barber->id, 'name' => 'Sakal & Yüz', 'icon' => '🪒', 'color' => '#6b7280', 'sort_order' => 2]);

        $barberServices = [
            ['cat' => $saçCat,   'name' => 'Erkek Saç Kesimi',       'duration' => 30,  'price' => 200],
            ['cat' => $saçCat,   'name' => 'Çocuk Saç Kesimi',       'duration' => 20,  'price' => 130],
            ['cat' => $saçCat,   'name' => 'Saç + Sakal Kombo',      'duration' => 60,  'price' => 350],
            ['cat' => $saçCat,   'name' => 'Saç Boyama (Erkek)',     'duration' => 60,  'price' => 400],
            ['cat' => $sakalCat, 'name' => 'Sakal Düzeltme',         'duration' => 20,  'price' => 150],
            ['cat' => $sakalCat, 'name' => 'Ütü Kayışı ile Tıraş',  'duration' => 40,  'price' => 250],
            ['cat' => $sakalCat, 'name' => 'Yüz Bakımı & Maske',    'duration' => 45,  'price' => 300],
        ];

        foreach ($barberServices as $s) {
            $service = Service::create([
                'business_id'      => $barber->id,
                'category_id'      => $s['cat']->id,
                'name'             => $s['name'],
                'duration_minutes' => $s['duration'],
                'price'            => $s['price'],
                'is_active'        => true,
            ]);
            $service->staff()->attach($barberStaff->pluck('id'));
        }

        // ── Nail Topia ────────────────────────────
        $nail = $businesses->firstWhere('slug', 'nail-topia');
        $nailStaff = $nail->staff;

        $elCat    = ServiceCategory::create(['business_id' => $nail->id, 'name' => 'El Bakımı', 'icon' => '💅', 'color' => '#f43f5e', 'sort_order' => 1]);
        $ayakCat  = ServiceCategory::create(['business_id' => $nail->id, 'name' => 'Ayak Bakımı', 'icon' => '🦶', 'color' => '#ec4899', 'sort_order' => 2]);
        $protezCat = ServiceCategory::create(['business_id' => $nail->id, 'name' => 'Protez Tırnak', 'icon' => '✨', 'color' => '#8b5cf6', 'sort_order' => 3]);

        $nailServices = [
            ['cat' => $elCat,    'name' => 'Kalıcı Oje',              'duration' => 60,  'price' => 350],
            ['cat' => $elCat,    'name' => 'Klasik Manikür',          'duration' => 45,  'price' => 200],
            ['cat' => $elCat,    'name' => 'Nail Art (Tasarım)',      'duration' => 90,  'price' => 500],
            ['cat' => $elCat,    'name' => 'French Manikür',          'duration' => 60,  'price' => 280],
            ['cat' => $ayakCat,  'name' => 'Kalıcı Oje (Ayak)',      'duration' => 60,  'price' => 300],
            ['cat' => $ayakCat,  'name' => 'Pedikür',                 'duration' => 60,  'price' => 250],
            ['cat' => $ayakCat,  'name' => 'Medikal Pedikür',         'duration' => 75,  'price' => 400],
            ['cat' => $protezCat,'name' => 'Akrilik Protez Tırnak',  'duration' => 120, 'price' => 800],
            ['cat' => $protezCat,'name' => 'Jel Tırnak (Tam Set)',   'duration' => 90,  'price' => 650],
            ['cat' => $protezCat,'name' => 'Protez Dolgu',            'duration' => 60,  'price' => 400],
        ];

        foreach ($nailServices as $s) {
            $service = Service::create([
                'business_id'      => $nail->id,
                'category_id'      => $s['cat']->id,
                'name'             => $s['name'],
                'duration_minutes' => $s['duration'],
                'price'            => $s['price'],
                'is_active'        => true,
            ]);
            $service->staff()->attach($nailStaff->pluck('id'));
        }
    }
}
