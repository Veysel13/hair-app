@extends('layouts.auth')

@section('content')
    <div class="animate-fade-in-up">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 rounded-2xl gradient-brand mb-4 shadow-lg shadow-purple-500/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Salonim</h1>
            <p class="text-sm text-[#7070a0] mt-1">Admin Paneli</p>
        </div>

        {{-- Card --}}
        <div class="glass-card rounded-2xl p-8 shadow-2xl">
            <h2 class="text-lg font-semibold text-white mb-1">Hoş Geldiniz 👋</h2>
            <p class="text-sm text-[#7070a0] mb-6">Devam etmek için giriş yapın</p>

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl px-4 py-3 mb-5">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="form-label" for="email">E-posta</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input"
                        placeholder="ornek@salonim.com.tr" autofocus required>
                </div>

                <div>
                    <label class="form-label" for="password">Şifre</label>
                    <input id="password" type="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded accent-purple-500">
                        <span class="text-sm text-[#a0a0c0]">Beni hatırla</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Giriş Yap
                </button>
            </form>

            <p class="text-center text-xs text-[#7070a0] mt-6">
                Test hesabı: <span class="text-[#e879f9]">admin@salanim.com.tr</span> / <span
                    class="text-[#e879f9]">password</span>
            </p>
        </div>
    </div>
@endsection