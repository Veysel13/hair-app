@extends('layouts.auth')

@section('content')
<div class="animate-fade-in-up my-6">
    {{-- Logo --}}
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl gradient-brand mb-3 shadow-lg shadow-purple-500/30">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <h1 class="text-xl font-extrabold text-white">Salonim</h1>
        <p class="text-xs text-[#7070a0] mt-0.5">Salon Sahibi Kayıt Formu</p>
    </div>

    {{-- Card --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 shadow-2xl">
        <h2 class="text-base font-bold text-white mb-1">Salonunuzu Kaydedin 🚀</h2>
        <p class="text-xs text-[#7070a0] mb-5">Birkaç adımda dijital randevu yönetimine başlayın</p>

        @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-xs rounded-xl px-4 py-3 mb-5">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('register.business.submit') }}" class="space-y-4">
            @csrf

            <div class="border-b border-white/5 pb-3">
                <h3 class="text-xs font-bold text-[#e879f9] uppercase tracking-wider mb-3">👤 Yetkili Bilgileri</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-[10px]" for="name">Ad Soyad</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            class="form-input text-xs" placeholder="Örn: Ahmet Yılmaz" required>
                    </div>
                    <div>
                        <label class="form-label text-[10px]" for="phone">Telefon Numarası</label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                            class="form-input text-xs" placeholder="Örn: 05321234567" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                    <div>
                        <label class="form-label text-[10px]" for="email">E-posta Adresi</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="form-input text-xs" placeholder="ahmet@example.com" required>
                    </div>
                    <div>
                        <label class="form-label text-[10px]" for="password">Şifre</label>
                        <input id="password" type="password" name="password"
                            class="form-input text-xs" placeholder="En az 6 karakter" required>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xs font-bold text-[#e879f9] uppercase tracking-wider mb-3">🏢 Salon Bilgileri</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-[10px]" for="business_name">Salon Adı</label>
                        <input id="business_name" type="text" name="business_name" value="{{ old('business_name') }}"
                            class="form-input text-xs" placeholder="Örn: Makas Kuaför" required>
                    </div>
                    <div>
                        <label class="form-label text-[10px]" for="business_type">Salon Türü</label>
                        <select id="business_type" name="business_type" class="form-input text-xs" required>
                            <option value="">Seçiniz...</option>
                            <option value="hair_salon" {{ old('business_type') === 'hair_salon' ? 'selected' : '' }}>Kuaför</option>
                            <option value="barber" {{ old('business_type') === 'barber' ? 'selected' : '' }}>Berber</option>
                            <option value="nail_salon" {{ old('business_type') === 'nail_salon' ? 'selected' : '' }}>Tırnak Salonu</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                    <div>
                        <label class="form-label text-[10px]" for="city">Şehir</label>
                        <input id="city" type="text" name="city" value="{{ old('city') ?? 'İstanbul' }}"
                            class="form-input text-xs" required>
                    </div>
                    <div>
                        <label class="form-label text-[10px]" for="district">İlçe</label>
                        <input id="district" type="text" name="district" value="{{ old('district') }}"
                            class="form-input text-xs" placeholder="Örn: Kadıköy" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full mt-4 font-bold text-xs py-3">
                Kayıt Ol & Salonu Oluştur 🎉
            </button>
        </form>

        <p class="text-center text-xs text-[#7070a0] mt-6">
            Zaten hesabınız var mı? 
            <a href="{{ route('login') }}" class="text-[#e879f9] hover:underline font-semibold">Giriş Yapın</a>
        </p>
    </div>
</div>
@endsection
