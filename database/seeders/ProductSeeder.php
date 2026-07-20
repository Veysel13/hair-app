<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $admin      = User::where('role', 'admin')->first();
        $businesses = Business::all();

        $productSets = [
            'lux-hair-salon' => [
                ['name' => 'Wella Illumina Boya 60ml',  'brand' => 'Wella',   'category' => 'Saç Boyası',  'unit' => 'adet', 'stock' => 45, 'min' => 10, 'purchase' => 85,  'sale' => 0,   'for_sale' => false],
                ['name' => 'Wella Oxidant %6 1000ml',   'brand' => 'Wella',   'category' => 'Oksidan',     'unit' => 'adet', 'stock' => 20, 'min' => 5,  'purchase' => 120, 'sale' => 0,   'for_sale' => false],
                ['name' => 'Olaplex No.3 Bond Perfector','brand' => 'Olaplex','category' => 'Saç Bakımı',  'unit' => 'adet', 'stock' => 12, 'min' => 3,  'purchase' => 280, 'sale' => 480, 'for_sale' => true],
                ['name' => 'Kerasilk Keratin 500ml',    'brand' => 'Goldwell','category' => 'Keratin',     'unit' => 'adet', 'stock' => 8,  'min' => 2,  'purchase' => 850, 'sale' => 0,   'for_sale' => false],
                ['name' => 'L\'Oreal Professionnel Maske 500ml','brand' => 'L\'Oreal','category' => 'Bakım Maskesi','unit' => 'adet', 'stock' => 15, 'min' => 4, 'purchase' => 320, 'sale' => 550, 'for_sale' => true],
                ['name' => 'Alüminyum Folyo 100m',      'brand' => 'Genel',   'category' => 'Sarf',        'unit' => 'adet', 'stock' => 6,  'min' => 2,  'purchase' => 85,  'sale' => 0,   'for_sale' => false],
                ['name' => 'Boyama Fırçası',             'brand' => 'Comair',  'category' => 'Ekipman',    'unit' => 'adet', 'stock' => 10, 'min' => 3,  'purchase' => 45,  'sale' => 0,   'for_sale' => false],
            ],
            'demir-barber' => [
                ['name' => 'Barba Italiana Sakal Yağı 30ml','brand' => 'Barba','category' => 'Sakal Bakımı','unit' => 'adet', 'stock' => 20, 'min' => 5, 'purchase' => 120, 'sale' => 220, 'for_sale' => true],
                ['name' => 'Proraso Tıraş Kremi 150ml',  'brand' => 'Proraso','category' => 'Tıraş',      'unit' => 'adet', 'stock' => 18, 'min' => 5,  'purchase' => 85,  'sale' => 160, 'for_sale' => true],
                ['name' => 'American Crew Wax 85g',      'brand' => 'American Crew','category' => 'Şekillendirici','unit' => 'adet', 'stock' => 25, 'min' => 8, 'purchase' => 95, 'sale' => 180, 'for_sale' => true],
                ['name' => 'Jilet (100\'lü paket)',       'brand' => 'Feather', 'category' => 'Sarf',       'unit' => 'paket','stock' => 8,  'min' => 2,  'purchase' => 120, 'sale' => 0,   'for_sale' => false],
            ],
            'nail-topia' => [
                ['name' => 'OPI Gelcolor Nail Polish',   'brand' => 'OPI',     'category' => 'Kalıcı Oje', 'unit' => 'adet', 'stock' => 80, 'min' => 20, 'purchase' => 120, 'sale' => 0,   'for_sale' => false],
                ['name' => 'Akriliz Pudra 150g',         'brand' => 'Cuccio',  'category' => 'Akrilik',    'unit' => 'adet', 'stock' => 5,  'min' => 2,  'purchase' => 350, 'sale' => 0,   'for_sale' => false],
                ['name' => 'UV/LED Jel Top Coat 30ml',  'brand' => 'IBD',     'category' => 'Jel',        'unit' => 'adet', 'stock' => 12, 'min' => 4,  'purchase' => 180, 'sale' => 0,   'for_sale' => false],
                ['name' => 'Cuticle Oil 15ml',          'brand' => 'CND',     'category' => 'Bakım',      'unit' => 'adet', 'stock' => 30, 'min' => 8,  'purchase' => 75,  'sale' => 150, 'for_sale' => true],
                ['name' => 'Tırnak Törpüsü (10\'lu)',   'brand' => 'Genel',   'category' => 'Sarf',       'unit' => 'paket','stock' => 2,  'min' => 3,  'purchase' => 25,  'sale' => 0,   'for_sale' => false], // kritik stok
            ],
        ];

        foreach ($businesses as $business) {
            $set = $productSets[$business->slug] ?? [];
            foreach ($set as $p) {
                $product = Product::create([
                    'business_id'    => $business->id,
                    'name'           => $p['name'],
                    'brand'          => $p['brand'],
                    'category'       => $p['category'],
                    'unit'           => $p['unit'],
                    'current_stock'  => $p['stock'],
                    'min_stock_level'=> $p['min'],
                    'purchase_price' => $p['purchase'],
                    'sale_price'     => $p['sale'] > 0 ? $p['sale'] : null,
                    'is_for_sale'    => $p['for_sale'],
                    'is_active'      => true,
                ]);

                // Başlangıç stok hareketi
                StockMovement::create([
                    'business_id'=> $business->id,
                    'product_id' => $product->id,
                    'type'       => 'in',
                    'quantity'   => $p['stock'],
                    'unit_price' => $p['purchase'],
                    'notes'      => 'Açılış stoğu',
                    'created_by' => $admin->id,
                ]);
            }
        }
    }
}
