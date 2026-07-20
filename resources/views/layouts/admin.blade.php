<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') — HairApp Admin</title>
    <link rel="manifest" href="/manifest.json">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-[#0f0f14] text-[#f1f1f8] min-h-screen" x-data="{ sidebarOpen: true, mobileOpen: false }">

    {{-- ── Sidebar ─────────────────────────────────── --}}
    <aside
        :class="sidebarOpen ? 'w-64' : 'w-16'"
        class="fixed top-0 left-0 h-full bg-[#16161f] border-r border-white/5 z-30 transition-all duration-300 flex flex-col hidden lg:flex overflow-hidden">

        {{-- Logo --}}
        <div class="flex items-center gap-3 p-5 border-b border-white/5" style="min-height:4.5rem;">
            <div class="w-9 h-9 rounded-xl gradient-brand flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div x-show="sidebarOpen" x-transition.opacity class="overflow-hidden">
                <span class="text-base font-bold gradient-text whitespace-nowrap">HairApp</span>
                <p class="text-[10px] text-[#7070a0] whitespace-nowrap">Admin Panel</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 p-3 overflow-y-auto overflow-x-hidden space-y-1">
            @include('layouts.partials.sidebar-nav')
        </nav>

        {{-- Collapse Toggle --}}
        <div class="p-3 border-t border-white/5">
            <button @click="sidebarOpen = !sidebarOpen"
                class="w-full flex items-center justify-center gap-2 p-2 rounded-lg text-[#7070a0] hover:text-[#e879f9] hover:bg-white/5 transition-all text-sm">
                <svg x-show="sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M19 19l-7-7 7-7"/>
                </svg>
                <svg x-show="!sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
                <span x-show="sidebarOpen" x-transition.opacity class="text-xs whitespace-nowrap">Daralt</span>
            </button>
        </div>
    </aside>

    {{-- ── Mobile Sidebar Overlay ──────────────────── --}}
    <div x-show="mobileOpen" @click="mobileOpen=false"
         class="fixed inset-0 bg-black/60 z-20 lg:hidden" x-transition.opacity></div>
    <aside x-show="mobileOpen" x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
           class="fixed top-0 left-0 h-full w-64 bg-[#16161f] border-r border-white/5 z-30 flex flex-col lg:hidden">
        <div class="flex items-center gap-3 p-5 border-b border-white/5">
            <div class="w-9 h-9 rounded-xl gradient-brand flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="text-base font-bold gradient-text">HairApp</span>
        </div>
        <nav class="flex-1 p-3 overflow-y-auto space-y-1">
            @include('layouts.partials.sidebar-nav')
        </nav>
    </aside>

    {{-- ── Main Content ─────────────────────────────── --}}
    <div :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-16'" class="transition-all duration-300 min-h-screen flex flex-col">

        {{-- Topbar --}}
        <header class="sticky top-0 z-20 bg-[#0f0f14]/90 backdrop-blur-sm border-b border-white/5 flex items-center justify-between px-4 lg:px-6 h-16">
            <div class="flex items-center gap-3">
                {{-- Mobile menu button --}}
                <button @click="mobileOpen=true" class="lg:hidden p-2 rounded-lg text-[#7070a0] hover:text-white hover:bg-white/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-base font-semibold text-white">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-xs text-[#7070a0]">@yield('page-subtitle', '')</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Notifications Dropdown --}}
                <div x-data="{ 
                    open: false, 
                    unreadCount: {{ Auth::user()->unreadNotifications->count() }},
                    markAsRead(id, event) {
                        fetch('/admin/notifications/' + id + '/read', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.unreadCount = Math.max(0, this.unreadCount - 1);
                                event.target.closest('.notification-item').remove();
                            }
                        });
                    }
                }" class="relative">
                    <button @click="open = !open" class="p-2 rounded-lg text-[#7070a0] hover:text-white hover:bg-white/5 transition-colors focus:outline-none">
                        <div class="relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <template x-if="unreadCount > 0">
                                <span class="absolute -top-1.5 -right-1.5 min-w-4 h-4 px-1 bg-red-500 text-white text-[9px] font-extrabold rounded-full flex items-center justify-center border border-[#0f0f14]" x-text="unreadCount"></span>
                            </template>
                        </div>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition.origin.top.right
                         class="absolute right-0 top-full mt-2 w-80 sm:w-96 min-w-[300px] sm:min-w-[360px] glass-card rounded-2xl p-4 shadow-2xl z-30" style="display: none;">
                        <div class="flex items-center justify-between border-b border-white/5 pb-2.5 mb-2.5">
                            <span class="text-xs font-bold text-white uppercase tracking-wider">Bildirimler</span>
                            <template x-if="unreadCount > 0">
                                <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-[#e879f9] hover:underline font-semibold">Tümünü Oku</button>
                                </form>
                            </template>
                        </div>

                        <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                            @forelse(Auth::user()->unreadNotifications->take(5) as $notif)
                            <div class="notification-item flex items-start gap-3 p-2.5 rounded-xl hover:bg-white/[0.04] bg-white/[0.01] border border-white/5 transition-all">
                                <div class="w-2.5 h-2.5 mt-1.5 rounded-full bg-purple-500 flex-shrink-0 shadow-lg shadow-purple-500/30"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-white/90 leading-relaxed font-medium">{{ $notif->data['message'] ?? 'Yeni talep var.' }}</p>
                                    <p class="text-[10px] text-[#7070a0] mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                                <button @click="markAsRead('{{ $notif->id }}', $event)" class="text-[#7070a0] hover:text-green-400 p-1.5 flex-shrink-0 transition-colors rounded-lg hover:bg-white/5" title="Okundu işaretle">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </div>
                            @empty
                            <div class="text-center py-10 text-[#7070a0] text-xs">
                                Okunmamış bildirim bulunmuyor.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- User dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open=!open" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-white/5 transition-colors">
                        <div class="w-8 h-8 rounded-lg gradient-brand flex items-center justify-center text-white text-sm font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-xs font-semibold text-white leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-[#7070a0] capitalize">{{ Auth::user()->role }}</p>
                        </div>
                        <svg class="w-4 h-4 text-[#7070a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open=false" x-transition.origin.top.right
                         class="absolute right-0 top-full mt-2 w-48 glass-card rounded-xl p-1 shadow-2xl">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm text-[#ef4444] hover:bg-red-500/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Çıkış Yap
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl text-sm">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
            <button @click="show=false" class="ml-auto text-green-400/60 hover:text-green-400">✕</button>
        </div>
        @endif

        @if(session('error') || $errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mx-4 lg:mx-6 mt-4 flex items-start gap-3 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                @if(session('error')){{ session('error') }}@endif
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
            <button @click="show=false" class="ml-auto text-red-400/60 hover:text-red-400">✕</button>
        </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 p-4 lg:p-6 animate-fade-in-up">
            @yield('content')
        </main>
    </div>

    {{-- PWA Install Banner --}}
    <div id="pwa-install-banner" class="fixed bottom-6 left-4 right-4 md:left-auto md:right-4 md:w-96 z-50 glass-card p-4 rounded-2xl border border-purple-500/20 shadow-2xl flex flex-col gap-3 transition-all duration-300 transform translate-y-32 opacity-0 pointer-events-none">
        <div class="flex items-start gap-3">
            <div class="w-12 h-12 rounded-xl gradient-brand flex items-center justify-center flex-shrink-0 text-white text-xl font-black shadow-lg shadow-purple-500/20">
                H
            </div>
            <div class="flex-1 min-w-0">
                <h4 id="pwa-title" class="text-sm font-bold text-white">HairApp'i Masaüstüne Yükleyin</h4>
                <p id="pwa-description" class="text-xs text-[#a0a0c0] mt-1 leading-relaxed">Yönetici paneline hızlıca erişmek, bildirimleri anlık takip etmek için uygulamasını yükleyin.</p>
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
                Paneli Yükle 🚀
            </button>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
