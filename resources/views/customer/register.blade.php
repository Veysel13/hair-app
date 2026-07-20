@extends('layouts.customer')
@section('title', 'Müşteri Kaydı')

@section('content')
<div class="max-w-md mx-auto space-y-4 animate-fade-in-up">

    <div class="text-center py-6">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl gradient-brand mb-3 shadow-lg shadow-purple-500/20">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <h2 class="text-xl font-extrabold text-white">Yeni Hesap Oluştur</h2>
        <p class="text-xs text-[#7070a0] mt-1">Hemen kayıt olun ve kolayca randevu alın</p>
    </div>

    <div class="glass-card rounded-3xl p-6 shadow-2xl">
        <form method="POST" action="{{ route('customer.register.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label class="form-label text-[10px]">Ad Soyad *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input text-xs" placeholder="Adınız ve soyadınız..." required autofocus>
            </div>

            <div>
                <label class="form-label text-[10px]">Telefon Numarası *</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" class="form-input text-xs" placeholder="Örn: 05321234567" required>
            </div>

            <div>
                <label class="form-label text-[10px]">E-posta Adresi *</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-input text-xs" placeholder="ornek@example.com" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label text-[10px]">Şifre *</label>
                    <input type="password" name="password" class="form-input text-xs" placeholder="••••••••" required>
                </div>
                <div>
                    <label class="form-label text-[10px]">Şifre Tekrarı *</label>
                    <input type="password" name="password_confirmation" class="form-input text-xs" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full text-xs py-3 mt-2">
                Hesap Oluştur & Giriş Yap
            </button>
        </form>
    </div>

    <div class="text-center">
        <p class="text-xs text-[#7070a0]">
            Zaten hesabınız var mı? <a href="{{ route('customer.login') }}" class="text-[#e879f9] hover:underline font-semibold">Giriş yapın</a>
        </p>
    </div>

</div>
@endsection
