<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    private function business(): Business
    {
        $user = Auth::user();
        return $user->isAdmin() ? Business::first() : $user->businesses()->firstOrFail();
    }

    public function index(Request $request)
    {
        $business = $this->business();
        $query    = CustomerProfile::with('customer')
            ->where('business_id', $business->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            );
        }
        if ($request->filled('vip')) $query->where('is_vip', true);
        if ($request->filled('inactive')) $query->inactive(90);

        $profiles = $query->orderByDesc('last_visit_at')->paginate(20);

        return view('admin.customers.index', compact('business', 'profiles'));
    }

    public function show(CustomerProfile $customerProfile)
    {
        $customerProfile->load([
            'customer',
            'treatmentRecords.service',
            'treatmentRecords.staff',
        ]);

        $appointments = \App\Models\Appointment::with(['services.service', 'staff', 'review'])
            ->where('business_id', $customerProfile->business_id)
            ->where('customer_id', $customerProfile->customer_id)
            ->orderByDesc('appointment_date')
            ->get();

        return view('admin.customers.show', compact('customerProfile', 'appointments'));
    }

    public function update(Request $request, CustomerProfile $customerProfile)
    {
        $data = $request->validate([
            'allergies' => 'nullable|string|max:1000',
            'hair_type' => 'nullable|string|max:100',
            'hair_color'=> 'nullable|string|max:50',
            'skin_type' => 'nullable|string|max:50',
            'notes'     => 'nullable|string|max:2000',
            'is_vip'    => 'boolean',
        ]);

        $customerProfile->update($data);
        return back()->with('success', 'Müşteri profili güncellendi.');
    }

    public function storeTreatmentRecord(Request $request, CustomerProfile $customerProfile)
    {
        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'staff_id'       => 'required|exists:staff,id',
            'service_id'     => 'required|exists:services,id',
            'formula'        => 'nullable|string',
            'products_used'  => 'nullable|string',
            'notes'          => 'nullable|string|max:2000',
            'before_image'   => 'nullable|image|max:4096',
            'after_image'    => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('before_image')) {
            $data['before_image'] = $request->file('before_image')->store('treatments', 'public');
        }
        if ($request->hasFile('after_image')) {
            $data['after_image'] = $request->file('after_image')->store('treatments', 'public');
        }

        // Convert simple line-separated or comma-separated lists to array for formula and products_used
        $data['formula'] = $request->filled('formula') 
            ? array_map('trim', explode(',', $request->formula)) 
            : [];
            
        $data['products_used'] = $request->filled('products_used') 
            ? array_map('trim', explode(',', $request->products_used)) 
            : [];

        $customerProfile->treatmentRecords()->create($data);

        return back()->with('success', 'Uygulama teknik kaydı başarıyla eklendi.');
    }
}
