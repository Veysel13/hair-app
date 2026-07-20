<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BusinessSeeder::class,
            StaffSeeder::class,
            ServiceSeeder::class,
            AppointmentSeeder::class,
            ProductSeeder::class,
            CampaignSeeder::class,
        ]);
    }
}
