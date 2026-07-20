<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    private function business(): Business
    {
        $user = Auth::user();
        return $user->isAdmin() ? Business::first() : $user->businesses()->firstOrFail();
    }

    public function index()
    {
        $business  = $this->business();
        $campaigns = $business->campaigns()->withCount('usages')->orderByDesc('created_at')->get();
        return view('admin.campaigns.index', compact('business', 'campaigns'));
    }

    public function create()
    {
        $business = $this->business();
        $services = $business->activeServices;
        return view('admin.campaigns.create', compact('business', 'services'));
    }

    public function store(Request $request)
    {
        $business = $this->business();
        $data = $request->validate([
            'name'                   => 'required|string|max:150',
            'description'            => 'nullable|string|max:500',
            'type'                   => 'required|in:discount,free_service,package,birthday,win_back,flash',
            'discount_type'          => 'nullable|in:percentage,fixed',
            'discount_value'         => 'nullable|numeric|min:0',
            'min_booking_amount'     => 'nullable|numeric|min:0',
            'applicable_service_ids' => 'nullable|array',
            'applicable_days'        => 'nullable|array',
            'applicable_time_start'  => 'nullable|date_format:H:i',
            'applicable_time_end'    => 'nullable|date_format:H:i',
            'starts_at'              => 'required|date',
            'ends_at'                => 'nullable|date|after:starts_at',
            'max_uses'               => 'nullable|integer|min:1',
            'max_uses_per_customer'  => 'nullable|integer|min:1',
            'code'                   => 'nullable|string|max:50|unique:campaigns,code',
            'is_active'              => 'boolean',
        ]);

        $business->campaigns()->create($data);
        return redirect()->route('admin.campaigns.index')->with('success', 'Kampanya oluşturuldu.');
    }

    public function toggleActive(Campaign $campaign)
    {
        $campaign->update(['is_active' => ! $campaign->is_active]);
        $label = $campaign->is_active ? 'aktif' : 'pasif';
        return back()->with('success', "Kampanya {$label} edildi.");
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('admin.campaigns.index')->with('success', 'Kampanya silindi.');
    }
}
