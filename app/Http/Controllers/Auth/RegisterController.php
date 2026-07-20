<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegisterForm(): View
    {
        return view('auth.register_business');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'         => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password'      => ['required', 'string', 'min:6'],
            'business_name' => ['required', 'string', 'max:100'],
            'business_type' => ['required', 'string', 'in:hair_salon,barber,nail_salon'],
            'city'          => ['required', 'string', 'max:50'],
            'district'      => ['required', 'string', 'max:50'],
        ]);

        // 1. Create Business Owner User
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'role'     => 'business_owner',
        ]);

        // 2. Create Business
        Business::create([
            'owner_id'  => $user->id,
            'name'      => $data['business_name'],
            'slug'      => Str::slug($data['business_name']) . '-' . rand(100, 999), // Slug overlap safety
            'category'  => $data['business_type'],
            'phone'     => $data['phone'],
            'email'     => $data['email'],
            'address'   => $data['district'] . ', ' . $data['city'],
            'city'      => $data['city'],
            'district'  => $data['district'],
            'is_active' => true,
        ]);

        // 3. Authenticate User
        Auth::login($user);

        // 4. Redirect with Onboarding Welcome Alert
        return redirect()->route('admin.dashboard')
            ->with('success', 'Salonunuz başarıyla kaydedildi! Yönetim paneline hoş geldiniz. Personel ve hizmetlerinizi ekleyerek başlayabilirsiniz.');
    }
}
