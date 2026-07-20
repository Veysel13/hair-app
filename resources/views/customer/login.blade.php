@extends('layouts.customer')
@section('title', 'Müşteri Girişi')

@section('content')
<div class="max-w-md mx-auto space-y-4 animate-fade-in-up">
    
    <div class="text-center py-6">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl gradient-brand mb-3 shadow-lg shadow-purple-500/20">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <h2 class="text-xl font-extrabold text-white">Müşteri Girişi</h2>
        <p class="text-xs text-[#7070a0] mt-1">Hesabınıza giriş yaparak randevularınızı takip edin</p>
    </div>

    <div class="glass-card rounded-3xl p-6 shadow-2xl">
        <form method="POST" action="{{ route('customer.login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label class="form-label text-[10px]">Telefon Numarası</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" class="form-input text-xs" placeholder="Örn: 05321234567" required autofocus>
            </div>

            <div>
                <label class="form-label text-[10px]">Şifre</label>
                <input type="password" name="password" class="form-input text-xs" placeholder="••••••••" required>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded accent-purple-500">
                    <span class="text-xs text-[#a0a0c0]">Beni hatırla</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-full text-xs py-3 mt-2">
                Giriş Yap
            </button>
        </form>
    </div>

    <div class="text-center">
        <p class="text-xs text-[#7070a0]">
            Hesabınız yok mu? <a href="{{ route('customer.register') }}" class="text-[#e879f9] hover:underline font-semibold">Hemen kayıt olun</a>
        </p>
    </div>

</div>
@endsection
