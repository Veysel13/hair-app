<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\BusinessSettingsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Customer\SearchController as CustomerSearchController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;

// ── Müşteri Arayüzü (Ana Sayfa & Keşif) ──────────────
Route::get('/', [CustomerHomeController::class, 'index'])->name('home');
Route::get('/search', [CustomerSearchController::class, 'index'])->name('search');

// Rezervasyon Akışı
Route::get('/book/{business}', [CustomerBookingController::class, 'show'])->name('book.show');
Route::post('/book/{business}', [CustomerBookingController::class, 'store'])->name('book.store');
Route::post('/book/{business}/available-slots', [CustomerBookingController::class, 'getAvailableSlots'])->name('book.slots');

// Müşteri Giriş & Kayıt
Route::middleware('guest')->group(function () {
    Route::get('/customer/login', [CustomerProfileController::class, 'showLogin'])->name('customer.login');
    Route::post('/customer/login', [CustomerProfileController::class, 'login'])->name('customer.login.submit');
    Route::get('/customer/register', [CustomerProfileController::class, 'showRegister'])->name('customer.register');
    Route::post('/customer/register', [CustomerProfileController::class, 'register'])->name('customer.register.submit');
});

// Giriş Yapmış Müşteri Paneli
Route::middleware('auth')->group(function () {
    Route::get('/customer/dashboard', [CustomerProfileController::class, 'index'])->name('customer.dashboard');
    Route::get('/customer/profile', [CustomerProfileController::class, 'edit'])->name('customer.profile');
    Route::put('/customer/profile', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
    Route::post('/customer/appointments/{appointment}/reviews', [CustomerProfileController::class, 'storeReview'])->name('customer.reviews.store');
});

// ── Auth ──────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register/business', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegisterForm'])->name('register.business');
    Route::post('/register/business', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.business.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Admin Paneli ──────────────────────────────────
Route::prefix('admin')
    ->middleware('auth')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Randevular
        Route::get('/appointments',                    [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/create',             [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments',                   [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{appointment}',      [AppointmentController::class, 'show'])->name('appointments.show');
        Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');

        // Takvim
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

        // Personel
        Route::get('/staff',                [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create',         [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff',               [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit',   [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{staff}',        [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{staff}',     [StaffController::class, 'destroy'])->name('staff.destroy');

        // Hizmetler
        Route::get('/services',                 [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create',          [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services',                [ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit',  [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}',       [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}',    [ServiceController::class, 'destroy'])->name('services.destroy');

        // Müşteriler (CRM)
        Route::get('/customers',                           [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customerProfile}',         [CustomerController::class, 'show'])->name('customers.show');
        Route::put('/customers/{customerProfile}',         [CustomerController::class, 'update'])->name('customers.update');
        Route::post('/customers/{customerProfile}/treatment-records', [CustomerController::class, 'storeTreatmentRecord'])->name('customers.treatment-records.store');

        // Finans
        Route::get('/finance',               [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/finance/expenses',     [FinanceController::class, 'storeExpense'])->name('finance.expenses.store');
        Route::delete('/finance/expenses/{expense}', [FinanceController::class, 'destroyExpense'])->name('finance.expenses.destroy');

        // Stok / Ürünler
        Route::get('/products',                      [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create',               [ProductController::class, 'create'])->name('products.create');
        Route::post('/products',                     [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit',       [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}',            [ProductController::class, 'update'])->name('products.update');
        Route::post('/products/{product}/add-stock', [ProductController::class, 'addStock'])->name('products.addStock');

        // Kampanyalar
        Route::get('/campaigns',                     [CampaignController::class, 'index'])->name('campaigns.index');
        Route::get('/campaigns/create',              [CampaignController::class, 'create'])->name('campaigns.create');
        Route::post('/campaigns',                    [CampaignController::class, 'store'])->name('campaigns.store');
        Route::patch('/campaigns/{campaign}/toggle', [CampaignController::class, 'toggleActive'])->name('campaigns.toggle');
        Route::delete('/campaigns/{campaign}',       [CampaignController::class, 'destroy'])->name('campaigns.destroy');

        // Yorumlar
        Route::get('/reviews',                         [ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/reply',         [ReviewController::class, 'reply'])->name('reviews.reply');
        Route::patch('/reviews/{review}/visibility',   [ReviewController::class, 'toggleVisibility'])->name('reviews.visibility');

        // Salon Ayarları
        Route::get('/settings', [BusinessSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [BusinessSettingsController::class, 'update'])->name('settings.update');
        Route::put('/settings/hours', [BusinessSettingsController::class, 'updateHours'])->name('settings.hours.update');
        Route::post('/settings/images', [BusinessSettingsController::class, 'uploadImage'])->name('settings.images.store');
        Route::delete('/settings/images/{image}', [BusinessSettingsController::class, 'destroyImage'])->name('settings.images.destroy');

        // Bildirim Rotaları
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    });
