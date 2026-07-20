<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\CustomerProfile;
use App\Models\DailySummary;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user     = Auth::user();
        $business = $this->getBusiness($user);

        if (! $business) {
            return view('admin.no-business');
        }

        $today = now()->toDateString();

        // ── KPI Kartları ────────────────────────
        $todayAppointments = Appointment::where('business_id', $business->id)
            ->where('appointment_date', $today)
            ->get();

        $monthRevenue = Appointment::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereYear('appointment_date', now()->year)
            ->whereMonth('appointment_date', now()->month)
            ->sum('total_price');

        $totalCustomers = CustomerProfile::where('business_id', $business->id)->count();
        $newCustomers   = CustomerProfile::where('business_id', $business->id)
            ->whereMonth('created_at', now()->month)->count();

        $avgRating = Review::where('business_id', $business->id)
            ->where('is_visible', true)->avg('rating') ?? 0;

        $lowStockProducts = Product::where('business_id', $business->id)
            ->whereColumn('current_stock', '<=', 'min_stock_level')
            ->where('is_active', true)->count();

        // ── Son 7 Günün Gelir Grafiği ─────────
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $revenueChart[] = [
                'date'    => now()->subDays($i)->format('d M'),
                'revenue' => Appointment::where('business_id', $business->id)
                    ->where('status', 'completed')
                    ->where('appointment_date', $date)
                    ->sum('total_price'),
                'count' => Appointment::where('business_id', $business->id)
                    ->where('appointment_date', $date)->count(),
            ];
        }

        // ── Yaklaşan Randevular ───────────────
        $upcomingAppointments = Appointment::with(['customer', 'staff', 'services.service'])
            ->where('business_id', $business->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('appointment_date', '>=', $today)
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->limit(8)
            ->get();

        // ── Son Yorumlar ──────────────────────
        $latestReviews = Review::with('customer')
            ->where('business_id', $business->id)
            ->where('is_visible', true)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'business',
            'todayAppointments',
            'monthRevenue',
            'totalCustomers',
            'newCustomers',
            'avgRating',
            'lowStockProducts',
            'revenueChart',
            'upcomingAppointments',
            'latestReviews',
        ));
    }

    private function getBusiness(User $user): ?Business
    {
        if ($user->isAdmin()) {
            return Business::first();
        }
        return $user->businesses()->first();
    }
}
