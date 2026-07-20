<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HairApp') — Saç & Güzellik Randevusu</title>
    <link rel="manifest" href="/manifest.json">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-[#0f0f14] text-[#f1f1f8] min-h-screen flex flex-col font-sans pb-20 lg:pb-0">

    {{-- Top Header --}}
    <header class="sticky top-0 z-40 bg-[#0f0f14]/80 backdrop-blur-md border-b border-white/5 h-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl gradient-brand flex items-center justify-center text-white">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="text-lg font-extrabold gradient-text">HairApp</span>
            </a>

            <div class="flex items-center gap-3">
                @auth
                @if(Auth::user()->role === 'business_owner' || Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-[#e879f9] hover:underline font-bold mr-2 flex items-center gap-1">
                    ⚙️ Yönetim Paneli
                </a>
                @endif
                <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2 hover:bg-white/5 px-2.5 py-1.5 rounded-xl transition-all">
                    <div class="w-7 h-7 rounded-lg gradient-brand flex items-center justify-center text-white text-xs font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <span class="text-xs font-semibold text-white hidden sm:inline">{{ Auth::user()->name }}</span>
                </a>
                @else
                <a href="{{ route('login') }}" class="text-xs text-[#a0a0c0] hover:text-white transition-colors mr-2 flex items-center gap-1 font-semibold">
                    💼 Salon Girişi
                </a>
                <a href="{{ route('customer.login') }}" class="btn btn-secondary btn-sm">Giriş Yap</a>
                <a href="{{ route('customer.register') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">Kayıt Ol</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Main Content Area --}}
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-28">
        {{-- Flash Messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-5 flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl text-sm">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
            <button @click="show=false" class="ml-auto text-green-400/60 hover:text-green-400">✕</button>
        </div>
        @endif

        @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-5 flex items-start gap-3 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            <button @click="show=false" class="ml-auto text-red-400/60 hover:text-red-400">✕</button>
        </div>
        @endif

        @yield('content')
    </main>

    {{-- Bottom Navigation Bar for Mobile-style look & feel --}}
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-[#16161f]/95 backdrop-blur-lg border-t border-white/5 h-18 flex items-center justify-around px-4 shadow-2xl">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1.5 text-white/50 hover:text-[#e879f9] transition-all duration-200 {{ request()->routeIs('home') ? 'text-[#e879f9] scale-105 font-bold' : 'font-medium' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-[11px] sm:text-xs">Ana Sayfa</span>
        </a>

        <a href="{{ route('search') }}" class="flex flex-col items-center gap-1.5 text-white/50 hover:text-[#e879f9] transition-all duration-200 {{ request()->routeIs('search') ? 'text-[#e879f9] scale-105 font-bold' : 'font-medium' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="text-[11px] sm:text-xs">Keşfet</span>
        </a>

        <a href="{{ route('customer.dashboard') }}" class="flex flex-col items-center gap-1.5 text-white/50 hover:text-[#e879f9] transition-all duration-200 {{ request()->routeIs('customer.dashboard') ? 'text-[#e879f9] scale-105 font-bold' : 'font-medium' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-[11px] sm:text-xs">Randevularım</span>
        </a>

        <a href="{{ route('customer.profile') }}" class="flex flex-col items-center gap-1.5 text-white/50 hover:text-[#e879f9] transition-all duration-200 {{ request()->routeIs('customer.profile') ? 'text-[#e879f9] scale-105 font-bold' : 'font-medium' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-[11px] sm:text-xs">Profilim</span>
    </nav>

    {{-- PWA Install Banner --}}
    <div id="pwa-install-banner" class="fixed bottom-20 left-4 right-4 md:left-auto md:right-4 md:w-96 z-50 glass-card p-4 rounded-2xl border border-purple-500/20 shadow-2xl flex flex-col gap-3 transition-all duration-300 transform translate-y-32 opacity-0 pointer-events-none">
        <div class="flex items-start gap-3">
            <div class="w-12 h-12 rounded-xl gradient-brand flex items-center justify-center flex-shrink-0 text-white text-xl font-black shadow-lg shadow-purple-500/20">
                H
            </div>
            <div class="flex-1 min-w-0">
                <h4 id="pwa-title" class="text-sm font-bold text-white">HairApp'i Telefonunuza Yükleyin</h4>
                <p id="pwa-description" class="text-xs text-[#a0a0c0] mt-1 leading-relaxed">Randevularınızı daha hızlı almak ve anlık bildirimleri takip etmek için ana ekranınıza ekleyin.</p>
            </div>
            <button onclick="window.dismissPwaBanner()" class="text-[#7070a0] hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="pwa-buttons-container" class="flex items-center gap-2 mt-1">
            <button onclick="window.dismissPwaBanner()" class="btn btn-secondary text-xs py-2 px-3 flex-1">
                Daha Sonra
            </button>
            <button id="pwa-install-btn" class="btn btn-primary text-xs py-2 px-4 flex-1 font-bold">
                Uygulamayı Yükle 🚀
            </button>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
