<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $upcomingAppointments = Appointment::with(['business', 'staff', 'services.service'])
            ->where('customer_id', $user->id)
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();

        $pastAppointments = Appointment::with(['business', 'staff', 'services.service', 'review'])
            ->where('customer_id', $user->id)
            ->where(function ($q) {
                $q->where('appointment_date', '<', now()->toDateString())
                  ->orWhereIn('status', ['completed', 'cancelled', 'no_show']);
            })
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->paginate(10);

        return view('customer.dashboard', compact('upcomingAppointments', 'pastAppointments'));
    }

    public function edit(): View
    {
        return view('customer.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => [
                'required',
                'string',
                Rule::unique('users')->ignore($user->id),
            ],
            'birth_date' => 'nullable|date',
            'password'   => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->birth_date = $data['birth_date'];

        if ($request->filled('password')) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('customer.profile')->with('success', 'Profiliniz güncellendi.');
    }

    // ── Müşteri Auth Sayfaları ─────────────────

    public function showLogin(): View
    {
        return view('customer.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $credentials['phone'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('customer.dashboard'));
        }

        return back()->withErrors([
            'phone' => 'Telefon numarası veya şifre hatalı.',
        ])->onlyInput('phone');
    }

    public function showRegister(): View
    {
        return view('customer.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'phone'    => 'required|string|unique:users,phone',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'phone'    => $data['phone'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'customer',
        ]);

        Auth::login($user);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Hesabınız başarıyla oluşturuldu.');
    }

    public function storeReview(Request $request, Appointment $appointment): RedirectResponse
    {
        if ($appointment->customer_id !== Auth::id() || $appointment->status !== 'completed') {
            abort(403);
        }

        if ($appointment->review()->exists()) {
            return back()->with('error', 'Bu randevu için zaten yorum yaptınız.');
        }

        $data = $request->validate([
            'rating'       => 'required|integer|min:1|max:5',
            'staff_rating' => 'required|integer|min:1|max:5',
            'comment'      => 'nullable|string|max:1000',
        ]);

        $appointment->review()->create([
            'business_id' => $appointment->business_id,
            'customer_id' => Auth::id(),
            'staff_id'    => $appointment->staff_id,
            'rating'      => $data['rating'],
            'staff_rating'=> $data['staff_rating'],
            'comment'     => $data['comment'],
            'is_visible'  => true,
        ]);

        return back()->with('success', 'Değerlendirmeniz başarıyla kaydedildi, teşekkür ederiz!');
    }
}
