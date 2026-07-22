<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessImage;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::find(1);
        if (!$business) {
            $this->command->error('Business ID 1 not found.');
            return;
        }

        // 1. Copy generated images to public upload directories
        $logoSrc = '/Users/veyselakpinar/.gemini/antigravity-ide/brain/2e407a27-b2e1-447b-8294-9df8f3eb53df/lux_hair_logo_1784665295091.png';
        $coverSrc = '/Users/veyselakpinar/.gemini/antigravity-ide/brain/2e407a27-b2e1-447b-8294-9df8f3eb53df/lux_hair_cover_1784665313705.png';
        $gal1Src = '/Users/veyselakpinar/.gemini/antigravity-ide/brain/2e407a27-b2e1-447b-8294-9df8f3eb53df/lux_hair_gallery1_1784665339353.png';
        $gal2Src = '/Users/veyselakpinar/.gemini/antigravity-ide/brain/2e407a27-b2e1-447b-8294-9df8f3eb53df/lux_hair_gallery2_1784665368544.png';
        $gal3Src = '/Users/veyselakpinar/.gemini/antigravity-ide/brain/2e407a27-b2e1-447b-8294-9df8f3eb53df/lux_hair_gallery3_1784665399223.png';

        // Prepare destination directories
        File::ensureDirectoryExists(public_path('uploads/logos'));
        File::ensureDirectoryExists(public_path('uploads/covers'));
        File::ensureDirectoryExists(public_path('uploads/gallery/1'));

        // Copy files
        if (File::exists($logoSrc)) {
            File::copy($logoSrc, public_path('uploads/logos/lux_hair_logo.png'));
        }
        if (File::exists($coverSrc)) {
            File::copy($coverSrc, public_path('uploads/covers/lux_hair_cover.png'));
        }
        if (File::exists($gal1Src)) {
            File::copy($gal1Src, public_path('uploads/gallery/1/gallery1.png'));
        }
        if (File::exists($gal2Src)) {
            File::copy($gal2Src, public_path('uploads/gallery/1/gallery2.png'));
        }
        if (File::exists($gal3Src)) {
            File::copy($gal3Src, public_path('uploads/gallery/1/gallery3.png'));
        }

        // 2. Update Business details
        $business->update([
            'logo' => 'uploads/logos/lux_hair_logo.png',
            'cover_image' => 'uploads/covers/lux_hair_cover.png',
            'website' => 'https://luxhair.com',
            'instagram' => 'lux_hair_salon',
            'description' => 'Lux Hair Salon, alanında uzman kadrosu ve dünya markası ürünleriyle saç tasarım, renklendirme ve mikro kaynak konularında İstanbul\'un öncü salonlarından biridir. Kendinizi özel hissedeceğiniz lüks atmosferimize davetlisiniz.',
        ]);

        // 3. Clear and insert new gallery images
        BusinessImage::where('business_id', $business->id)->delete();
        BusinessImage::create([
            'business_id' => $business->id,
            'image_path'  => 'uploads/gallery/1/gallery1.png',
            'caption'     => 'Renklendirme ve Röfle İşlemi',
            'sort_order'  => 1,
        ]);
        BusinessImage::create([
            'business_id' => $business->id,
            'image_path'  => 'uploads/gallery/1/gallery2.png',
            'caption'     => 'Fön ve Kesim Sonucu',
            'sort_order'  => 2,
        ]);
        BusinessImage::create([
            'business_id' => $business->id,
            'image_path'  => 'uploads/gallery/1/gallery3.png',
            'caption'     => 'Saç Kesimi Detayları',
            'sort_order'  => 3,
        ]);

        // 4. Seed sample reviews
        Review::where('business_id', $business->id)->delete();
        $customers = User::where('role', 'customer')->take(3)->get();
        $staff = $business->staff()->first();

        if ($customers->count() >= 3 && $staff) {
            Review::create([
                'appointment_id' => 1,
                'business_id' => $business->id,
                'customer_id' => $customers[0]->id,
                'staff_id'    => $staff->id,
                'rating'      => 5,
                'staff_rating'=> 5,
                'comment'     => 'Yıllardır saçımı Filiz Hanım\'a emanet ederim. Renklendirmede tek geçerim!',
                'is_visible'  => true,
            ]);
            Review::create([
                'appointment_id' => 2,
                'business_id' => $business->id,
                'customer_id' => $customers[1]->id,
                'staff_id'    => $staff->id,
                'rating'      => 5,
                'staff_rating'=> 5,
                'comment'     => 'Salonun temizliği ve çalışanların güleryüzlülüğü harika. Çok memnun kaldım.',
                'is_visible'  => true,
            ]);
            Review::create([
                'appointment_id' => 3,
                'business_id' => $business->id,
                'customer_id' => $customers[2]->id,
                'staff_id'    => $staff->id,
                'rating'      => 4,
                'staff_rating'=> 5,
                'comment'     => 'Kesim mükemmel oldu. Kahve ikramları için ayrıca teşekkürler.',
                'is_visible'  => true,
            ]);
        }
    }
}
