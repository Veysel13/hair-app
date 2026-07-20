<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // En yüksek puanlı 5 işletmeyi getir
        $featuredBusinesses = Business::where('is_active', true)
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get();

        return view('customer.home', compact('featuredBusinesses'));
    }
}
