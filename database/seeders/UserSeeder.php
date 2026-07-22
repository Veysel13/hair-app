<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────
        User::create([
            'name'     => 'Admin Kullanıcı',
            'email'    => 'admin@hairapp.com',
            'password' => Hash::make('123456'),
            'role'     => 'admin',
            'phone'    => '05001234567',
            'notification_preferences' => ['sms' => true, 'email' => true, 'whatsapp' => false],
        ]);

        // ── İşletme Sahipleri ──────────────────
        User::create([
            'name'       => 'Ayşe Kaya',
            'email'      => 'ayse@luxhair.com',
            'password'   => Hash::make('123456'),
            'role'       => 'business_owner',
            'phone'      => '05321234567',
            'birth_date' => '1985-03-15',
            'notification_preferences' => ['sms' => true, 'email' => true, 'whatsapp' => true],
        ]);

        User::create([
            'name'       => 'Mehmet Demir',
            'email'      => 'mehmet@demirbarber.com',
            'password'   => Hash::make('123456'),
            'role'       => 'business_owner',
            'phone'      => '05421234567',
            'birth_date' => '1980-07-22',
            'notification_preferences' => ['sms' => true, 'email' => false, 'whatsapp' => true],
        ]);

        User::create([
            'name'       => 'Zeynep Arslan',
            'email'      => 'zeynep@nailtopia.com',
            'password'   => Hash::make('123456'),
            'role'       => 'business_owner',
            'phone'      => '05531234567',
            'birth_date' => '1990-11-08',
            'notification_preferences' => ['sms' => true, 'email' => true, 'whatsapp' => true],
        ]);

        // ── Müşteriler ─────────────────────────
        $customers = [
            ['name' => 'Fatma Yılmaz',   'email' => 'fatma@example.com',   'phone' => '05361234567', 'birth_date' => '1992-05-10'],
            ['name' => 'Selin Çelik',    'email' => 'selin@example.com',   'phone' => '05371234568', 'birth_date' => '1995-08-20'],
            ['name' => 'Elif Şahin',     'email' => 'elif@example.com',    'phone' => '05381234569', 'birth_date' => '1988-01-30'],
            ['name' => 'Merve Koç',      'email' => 'merve@example.com',   'phone' => '05391234560', 'birth_date' => '1997-12-05'],
            ['name' => 'Büşra Öztürk',   'email' => 'busra@example.com',   'phone' => '05401234561', 'birth_date' => '1993-06-18'],
            ['name' => 'Hande Aydın',    'email' => 'hande@example.com',   'phone' => '05411234562', 'birth_date' => '1986-09-25'],
            ['name' => 'Neslihan Kurt',  'email' => 'neslihan@example.com', 'phone' => '05421234563', 'birth_date' => '1999-03-12'],
            ['name' => 'Gizem Çetin',    'email' => 'gizem@example.com',   'phone' => '05431234564', 'birth_date' => '1994-07-07'],
            ['name' => 'Derya Polat',    'email' => 'derya@example.com',   'phone' => '05441234565', 'birth_date' => '1991-02-14'],
            ['name' => 'Pınar Doğan',    'email' => 'pinar@example.com',   'phone' => '05451234566', 'birth_date' => '1996-10-28'],
            ['name' => 'Volkan Aslan',   'email' => 'volkan@example.com',  'phone' => '05461234567', 'birth_date' => '1987-04-16'],
            ['name' => 'Emre Kılıç',     'email' => 'emre@example.com',    'phone' => '05471234568', 'birth_date' => '1990-08-03'],
            ['name' => 'Ozan Yıldız',    'email' => 'ozan@example.com',    'phone' => '05481234569', 'birth_date' => '1993-11-22'],
            ['name' => 'Berk Güneş',     'email' => 'berk@example.com',    'phone' => '05491234560', 'birth_date' => '1998-05-09'],
            ['name' => 'Caner Bulut',    'email' => 'caner@example.com',   'phone' => '05501234561', 'birth_date' => '1985-12-31'],
            ['name' => 'Tarık Özkan',    'email' => 'tarik@example.com',   'phone' => '05511234562', 'birth_date' => '1992-06-14'],
            ['name' => 'Arda Şen',       'email' => 'arda@example.com',    'phone' => '05521234563', 'birth_date' => '1997-09-19'],
            ['name' => 'Mert Avcı',      'email' => 'mert@example.com',    'phone' => '05531234564', 'birth_date' => '1989-03-27'],
            ['name' => 'Savaş Turan',    'email' => 'savas@example.com',   'phone' => '05541234565', 'birth_date' => '1994-07-11'],
            ['name' => 'Kerem Erdoğan',  'email' => 'kerem@example.com',   'phone' => '05551234566', 'birth_date' => '1996-01-05'],
        ];

        foreach ($customers as $customer) {
            User::create(array_merge($customer, [
                'password' => Hash::make('123456'),
                'role'     => 'customer',
                'notification_preferences' => ['sms' => true, 'email' => true, 'whatsapp' => true],
            ]));
        }
    }
}
