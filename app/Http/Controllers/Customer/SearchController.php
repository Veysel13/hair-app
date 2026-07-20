<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        // Varsayılan koordinatlar (Merkez Kadıköy)
        $lat = (float) $request->get('latitude', 40.9901);
        $lng = (float) $request->get('longitude', 29.0204);

        $query = Business::where('is_active', true)->with('hours');

        // Kategori filtresi
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Arama sorgusu (isim veya açıklama)
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%");
            });
        }

        $businesses = $query->get();

        // Mesafeleri hesapla ve sırala
        $businesses = $businesses->map(function ($business) use ($lat, $lng) {
            $business->distance = $business->distanceTo($lat, $lng);
            return $business;
        });

        // Sıralama türü: distance (varsayılan) veya rating
        $sort = $request->get('sort', 'distance');
        if ($sort === 'rating') {
            $businesses = $businesses->sortByDesc('avg_rating');
        } else {
            $businesses = $businesses->sortBy('distance');
        }

        // Tarih/saat filtresi (Müsaitlik kontrolü: işletme o gün/saat açık mı?)
        if ($request->filled('date') && $request->filled('time')) {
            $dateString = $request->get('date');
            $timeString = $request->get('time');
            $dayOfWeek = date('w', strtotime($dateString)); // 0 = Pazar, 6 = Cumartesi

            $businesses = $businesses->filter(function ($business) use ($dayOfWeek, $timeString) {
                $hour = $business->hours->firstWhere('day_of_week', $dayOfWeek);
                if (!$hour || !$hour->is_open) {
                    return false;
                }
                $time = date('H:i:s', strtotime($timeString));
                return $time >= $hour->open_time && $time <= $hour->close_time;
            });
        }

        return view('customer.search', compact('businesses', 'lat', 'lng'));
    }
}
