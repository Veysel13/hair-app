<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Service;
use App\Models\Staff;
use App\Models\StaffWorkingHour;
use App\Models\User;
use App\Models\Appointment;
use App\Models\CustomerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private $business;
    private $staff;
    private $service;
    private $owner;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create a business owner
        $this->owner = User::create([
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'role' => 'business_owner',
        ]);

        // 2. Create a business
        $this->business = Business::create([
            'owner_id' => $this->owner->id,
            'name' => 'Test Hair Salon',
            'slug' => 'test-hair-salon',
            'type' => 'hair_salon',
            'phone' => '02161234567',
            'email' => 'test@example.com',
            'city' => 'İstanbul',
            'district' => 'Kadıköy',
            'address' => 'Test adresi',
            'latitude' => 40.9901,
            'longitude' => 29.0204,
            'is_active' => true,
        ]);

        // 3. Create a staff member
        $this->staff = Staff::create([
            'business_id' => $this->business->id,
            'name' => 'Stilist Derya',
            'title' => 'Baş Stilist',
            'is_active' => true,
            'display_order' => 1,
        ]);

        // 4. Create working hours for staff (Monday - 1 is Monday in php 'w' is 0 for Sunday, 1 for Monday)
        // Let's set day of week 1 (Monday) as working day
        StaffWorkingHour::create([
            'staff_id' => $this->staff->id,
            'day_of_week' => 1, // Monday
            'is_working' => true,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 5. Create a service
        $this->service = Service::create([
            'business_id' => $this->business->id,
            'name' => 'Saç Kesimi',
            'price' => 350,
            'duration_minutes' => 45,
            'is_active' => true,
        ]);
    }

    public function test_can_retrieve_available_slots()
    {
        // We test for a Monday in the future, e.g. 2026-07-20 (which is a Monday)
        $response = $this->postJson(route('book.slots', $this->business), [
            'date' => '2026-07-20',
            'staff_id' => $this->staff->id,
            'service_ids' => [$this->service->id],
        ]);

        $response->assertStatus(200);
        $slots = $response->json();
        
        // Output should be slots from 09:00 to 17:15 (since duration is 45 min and work ends at 18:00)
        $this->assertNotEmpty($slots);
        $this->assertContains('09:00', $slots);
        $this->assertContains('17:15', $slots);
        $this->assertNotContains('17:30', $slots); // 17:30 + 45min exceeds 18:00
    }

    public function test_guest_can_book_appointment_and_system_creates_user()
    {
        $response = $this->post(route('book.store', $this->business), [
            'staff_id' => $this->staff->id,
            'appointment_date' => '2026-07-20',
            'start_time' => '10:00',
            'service_ids' => [$this->service->id],
            'name' => 'Kemal Sunal',
            'phone' => '05999999999',
            'notes' => 'Kısa saç kesimi lütfen.',
        ]);

        // Assert redirect to customer dashboard
        $response->assertRedirect(route('customer.dashboard'));
        $response->assertSessionHas('success');

        // Assert user was created
        $user = User::where('phone', '05999999999')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Kemal Sunal', $user->name);
        $this->assertEquals('customer', $user->role);

        // Assert user is logged in
        $this->assertAuthenticatedAs($user);

        // Assert appointment was created
        $appointment = Appointment::where('customer_id', $user->id)->first();
        $this->assertNotNull($appointment);
        $this->assertEquals($this->business->id, $appointment->business_id);
        $this->assertEquals($this->staff->id, $appointment->staff_id);
        $this->assertEquals('2026-07-20', $appointment->appointment_date->format('Y-m-d'));
        $this->assertEquals('10:00', $appointment->start_time);
        $this->assertEquals('10:45:00', $appointment->end_time); // 10:00 + 45 mins
        $this->assertEquals(350, $appointment->total_price);
        $this->assertEquals('pending', $appointment->status);

        // Assert customer profile was created/updated in CRM
        $profile = CustomerProfile::where('customer_id', $user->id)
            ->where('business_id', $this->business->id)
            ->first();
        
        $this->assertNotNull($profile);
        $this->assertEquals(1, $profile->total_visits);
        $this->assertEquals(350, $profile->total_spent);
    }

    public function test_business_owner_can_register_and_create_business()
    {
        $response = $this->post(route('register.business.submit'), [
            'name'          => 'Metin Akpınar',
            'email'         => 'metin@example.com',
            'phone'         => '05888888888',
            'password'      => 'secretpassword',
            'business_name' => 'Akpınar Güzellik Salonu',
            'business_type' => 'hair_salon',
            'city'          => 'İzmir',
            'district'      => 'Bornova',
        ]);

        // Redirects to admin dashboard
        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');

        // User is created as business owner
        $user = User::where('email', 'metin@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Metin Akpınar', $user->name);
        $this->assertEquals('business_owner', $user->role);

        // Business record is automatically provisioned
        $business = Business::where('owner_id', $user->id)->first();
        $this->assertNotNull($business);
        $this->assertEquals('Akpınar Güzellik Salonu', $business->name);
        $this->assertEquals('hair_salon', $business->category);
        $this->assertEquals('İzmir', $business->city);
        $this->assertEquals('Bornova', $business->district);
        $this->assertTrue($business->is_active);

        // Session authentication
        $this->assertAuthenticatedAs($user);
    }

    public function test_business_owner_can_manage_settings_hours_and_gallery()
    {
        // 1. Authenticate the business owner
        $user = User::factory()->create(['role' => 'business_owner']);
        $business = Business::create([
            'owner_id' => $user->id,
            'name'     => 'Eski Salon',
            'slug'     => 'eski-salon',
            'category' => 'barber',
            'phone'    => '05555555555',
            'address'  => 'Eski Adres',
            'city'     => 'İstanbul',
            'district' => 'Kadıköy',
            'is_active'=> true,
        ]);

        $this->actingAs($user);

        // 2. Can view settings page and seeds business hours automatically
        $response = $this->get(route('admin.settings.edit'));
        $response->assertStatus(200);
        $this->assertDatabaseCount('business_hours', 7); // 7 days of hours auto-seeded

        // 3. Can update general details
        $response = $this->put(route('admin.settings.update'), [
            'name'               => 'Yeni Salon Adı',
            'category'           => 'hair_salon',
            'phone'              => '05000000000',
            'email'              => 'yeni@example.com',
            'address'            => 'Yeni Adres Sokak',
            'city'               => 'İstanbul',
            'district'           => 'Beşiktaş',
            'deposit_required'   => '1',
            'deposit_percentage' => 30,
            'cancellation_hours' => 12,
        ]);
        $response->assertRedirect(route('admin.settings.edit'));
        $response->assertSessionHas('success');

        $business->refresh();
        $this->assertEquals('Yeni Salon Adı', $business->name);
        $this->assertEquals('hair_salon', $business->category);
        $this->assertTrue($business->deposit_required);
        $this->assertEquals(30, $business->deposit_percentage);
        $this->assertEquals(12, $business->cancellation_hours);

        // 4. Can update working hours
        $response = $this->put(route('admin.settings.hours.update'), [
            'hours' => [
                0 => ['is_open' => '1', 'open_time' => '10:00', 'close_time' => '18:00'], // Sunday open
                1 => ['is_open' => '1', 'open_time' => '09:00', 'close_time' => '20:00'],
                2 => ['is_open' => '1', 'open_time' => '09:00', 'close_time' => '20:00'],
                3 => ['is_open' => '1', 'open_time' => '09:00', 'close_time' => '20:00'],
                4 => ['is_open' => '1', 'open_time' => '09:00', 'close_time' => '20:00'],
                5 => ['is_open' => '1', 'open_time' => '09:00', 'close_time' => '20:00'],
                6 => ['open_time' => '09:00', 'close_time' => '20:00'], // Closed (no is_open)
            ]
        ]);
        $response->assertRedirect(route('admin.settings.edit'));

        $sunday = $business->hours()->where('day_of_week', 0)->first();
        $this->assertTrue($sunday->is_open);
        $this->assertEquals('10:00', $sunday->open_time);

        $saturday = $business->hours()->where('day_of_week', 6)->first();
        $this->assertFalse($saturday->is_open);
    }

    public function test_appointment_creation_triggers_business_owner_notification_and_can_be_marked_read()
    {
        // 1. Create a guest booking request
        $response = $this->post(route('book.store', $this->business), [
            'staff_id'         => $this->staff->id,
            'appointment_date' => '2026-07-20',
            'start_time'       => '11:00',
            'service_ids'      => [$this->service->id],
            'name'             => 'Ahmet Kaya',
            'phone'            => '05777777777',
        ]);

        $response->assertRedirect(route('customer.dashboard'));

        // 2. Owner should have 1 unread notification
        $owner = $this->owner;
        $this->assertEquals(1, $owner->unreadNotifications->count());

        $notification = $owner->unreadNotifications->first();
        $this->assertEquals('Yeni randevu talebi: Ahmet Kaya - Saç Kesimi (11:00)', $notification->data['message']);

        // 3. Mark notification as read
        $this->actingAs($owner);
        $readResponse = $this->patch(route('admin.notifications.read', $notification->id));
        $readResponse->assertStatus(200);
        $readResponse->assertJson(['success' => true]);

        // 4. Owner should have 0 unread notifications
        $owner->refresh();
        $this->assertEquals(0, $owner->unreadNotifications->count());
    }
}
