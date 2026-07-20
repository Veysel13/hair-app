@extends('layouts.customer')
@section('title', 'Profil Bilgilerim')

@section('content')
<div class="max-w-md mx-auto space-y-4">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">Profil Bilgilerim</h3>

        <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label text-[10px]">Ad Soyad *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input text-xs" required>
            </div>

            <div>
                <label class="form-label text-[10px]">E-posta Adresi *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input text-xs" required>
            </div>

            <div>
                <label class="form-label text-[10px]">Telefon Numarası *</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input text-xs" required>
            </div>

            <div>
                <label class="form-label text-[10px]">Doğum Tarihi</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" class="form-input text-xs">
            </div>

            <div class="border-t border-white/5 pt-4 space-y-4">
                <p class="text-xs font-semibold text-white">Şifre Değiştir (İsteğe Bağlı)</p>
                <div>
                    <label class="form-label text-[10px]">Yeni Şifre</label>
                    <input type="password" name="password" class="form-input text-xs" placeholder="••••••">
                </div>
                <div>
                    <label class="form-label text-[10px]">Yeni Şifre (Tekrar)</label>
                    <input type="password" name="password_confirmation" class="form-input text-xs" placeholder="••••••">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full text-xs py-3 mt-4">
                Profilimi Güncelle
            </button>
        </form>
    </div>

    {{-- Logout --}}
    <div class="glass-card rounded-2xl p-4 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-white">Oturumu Kapat</p>
            <p class="text-[9px] text-[#7070a0]">Hesabınızdan güvenli çıkış yapın</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm text-[11px] px-4 py-2">
                Çıkış Yap
            </button>
        </form>
    </div>
</div>
@endsection
