<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    private function business(): Business
    {
        $user = Auth::user();
        return $user->isAdmin() ? Business::first() : $user->businesses()->firstOrFail();
    }

    public function index()
    {
        $business   = $this->business();
        $categories = $business->serviceCategories()->with('services')->get();
        $services   = $business->services()->with('category', 'staff')->orderBy('sort_order')->get();
        return view('admin.services.index', compact('business', 'categories', 'services'));
    }

    public function create()
    {
        $business   = $this->business();
        $categories = $business->serviceCategories;
        $staff      = $business->activeStaff;
        return view('admin.services.create', compact('business', 'categories', 'staff'));
    }

    public function store(Request $request)
    {
        $business = $this->business();
        $data = $request->validate([
            'category_id'      => 'nullable|exists:service_categories,id',
            'name'             => 'required|string|max:150',
            'description'      => 'nullable|string|max:500',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'price'            => 'required|numeric|min:0',
            'sort_order'       => 'nullable|integer|min:0',
            'staff_ids'        => 'nullable|array',
        ]);

        $service = $business->services()->create($data);
        if (! empty($data['staff_ids'])) {
            $service->staff()->attach($data['staff_ids']);
        }

        return redirect()->route('admin.services.index')->with('success', 'Hizmet eklendi.');
    }

    public function edit(Service $service)
    {
        $business   = $this->business();
        $categories = $business->serviceCategories;
        $staff      = $business->activeStaff;
        $service->load('staff');
        return view('admin.services.edit', compact('business', 'service', 'categories', 'staff'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'category_id'      => 'nullable|exists:service_categories,id',
            'name'             => 'required|string|max:150',
            'description'      => 'nullable|string|max:500',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'price'            => 'required|numeric|min:0',
            'is_active'        => 'boolean',
            'sort_order'       => 'nullable|integer|min:0',
            'staff_ids'        => 'nullable|array',
        ]);

        $service->update($data);
        $service->staff()->sync($data['staff_ids'] ?? []);

        return redirect()->route('admin.services.index')->with('success', 'Hizmet güncellendi.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Hizmet silindi.');
    }
}
