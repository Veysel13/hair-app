<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    private function business(): Business
    {
        $user = Auth::user();
        return $user->isAdmin() ? Business::first() : $user->businesses()->firstOrFail();
    }

    public function index(Request $request)
    {
        $business = $this->business();
        $query    = Review::with(['customer', 'staff', 'appointment'])
            ->where('business_id', $business->id);

        if ($request->filled('rating'))  $query->where('rating', $request->rating);
        if ($request->filled('visible')) $query->where('is_visible', $request->visible);
        if ($request->filled('replied')) {
            $request->replied === '1'
                ? $query->whereNotNull('business_reply')
                : $query->whereNull('business_reply');
        }

        $reviews = $query->latest()->paginate(20);
        $avgRating = Review::where('business_id', $business->id)->where('is_visible', true)->avg('rating');

        return view('admin.reviews.index', compact('business', 'reviews', 'avgRating'));
    }

    public function reply(Request $request, Review $review)
    {
        $request->validate(['reply' => 'required|string|max:1000']);
        $review->update([
            'business_reply'     => $request->reply,
            'business_replied_at'=> now(),
        ]);
        return back()->with('success', 'Yanıtınız kaydedildi.');
    }

    public function toggleVisibility(Review $review)
    {
        $review->update(['is_visible' => ! $review->is_visible]);
        return back()->with('success', $review->is_visible ? 'Yorum görünür yapıldı.' : 'Yorum gizlendi.');
    }
}
