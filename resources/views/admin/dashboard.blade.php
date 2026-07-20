@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Genel bakış — ' . now()->format('d F Y'))

@section('content')

{{-- ── KPI Kartları ──────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Bugünkü Randevular --}}
    <div class="stat-card">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/15 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="badge badge-info text-xs">Bugün</span>
        </div>
        <p class="text-3xl font-bold text-white">{{ $todayAppointments->count() }}</p>
        <p class="text-xs text-[#7070a0] mt-1">Randevu</p>
        <div class="flex gap-2 mt-2 flex-wrap">
            <span class="text-xs text-green-400">✓ {{ $todayAppointments->where('status','confirmed')->count() }} onaylı</span>
            <span class="text-xs text-yellow-400">⏳ {{ $todayAppointments->where('status','pending')->count() }} bekliyor</span>
        </div>
    </div>

    {{-- Aylık Gelir --}}
    <div class="stat-card">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-green-500/15 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="badge badge-success text-xs">Bu Ay</span>
        </div>
        <p class="text-2xl font-bold text-white">{{ number_format($monthRevenue, 0, ',', '.') }} ₺</p>
        <p class="text-xs text-[#7070a0] mt-1">Aylık Gelir</p>
    </div>

    {{-- Müşteri --}}
    <div class="stat-card">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-purple-500/15 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="badge badge-purple text-xs">+{{ $newCustomers }} yeni</span>
        </div>
        <p class="text-3xl font-bold text-white">{{ $totalCustomers }}</p>
        <p class="text-xs text-[#7070a0] mt-1">Toplam Müşteri</p>
    </div>

    {{-- Ortalama Puan --}}
    <div class="stat-card">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-yellow-500/15 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            @if($lowStockProducts > 0)
            <span class="badge badge-warning text-xs">{{ $lowStockProducts }} ürün kritik</span>
            @endif
        </div>
        <p class="text-3xl font-bold text-white">{{ number_format($avgRating, 1) }}</p>
        <p class="text-xs text-[#7070a0] mt-1">Ortalama Puan</p>
        <div class="flex gap-0.5 mt-1">
            @for($i=1; $i<=5; $i++)
                <svg class="w-3.5 h-3.5 {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-[#32324a]' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
        </div>
    </div>
</div>

{{-- ── İki Kolon: Grafik + Yaklaşan Randevular ─────── --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-4 mb-4">

    {{-- Gelir Grafiği (3/5) --}}
    <div class="xl:col-span-3 glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-semibold text-white">Son 7 Günün Geliri</h3>
                <p class="text-xs text-[#7070a0]">Tamamlanan randevular</p>
            </div>
            <a href="{{ route('admin.finance.index') }}" class="btn btn-secondary btn-sm">Tümünü Gör</a>
        </div>
        <div style="height:200px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Yaklaşan Randevular (2/5) --}}
    <div class="xl:col-span-2 glass-card rounded-2xl p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-white">Yaklaşan Randevular</h3>
            <a href="{{ route('admin.appointments.index') }}" class="text-xs text-[#e879f9] hover:underline">Tümü →</a>
        </div>
        <div class="space-y-2 flex-1 overflow-y-auto">
            @forelse($upcomingAppointments as $appt)
            <a href="{{ route('admin.appointments.show', $appt) }}"
               class="block p-3 rounded-xl bg-white/3 hover:bg-white/6 transition-colors border-l-2
               {{ match($appt->status) { 'confirmed'=>'border-blue-400', 'pending'=>'border-yellow-400', default=>'border-[#32324a]' } }}">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-white truncate">{{ $appt->customer->name }}</p>
                    <span class="text-[10px] text-[#7070a0]">{{ $appt->start_time }}</span>
                </div>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="text-[10px] text-[#a0a0c0]">{{ $appt->appointment_date->format('d M') }}</span>
                    <span class="text-[10px] text-[#7070a0]">•</span>
                    <span class="text-[10px] text-[#a0a0c0] truncate">{{ $appt->staff->name }}</span>
                </div>
            </a>
            @empty
            <div class="flex items-center justify-center h-full py-8 text-[#7070a0] text-sm">
                Yaklaşan randevu yok
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── Alt Kısım: Son Yorumlar + Hızlı Aksiyonlar ─── --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

    {{-- Son Yorumlar (3/5) --}}
    <div class="xl:col-span-3 glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-white">Son Yorumlar</h3>
            <a href="{{ route('admin.reviews.index') }}" class="text-xs text-[#e879f9] hover:underline">Tümü →</a>
        </div>
        <div class="space-y-3">
            @forelse($latestReviews as $review)
            <div class="flex items-start gap-3 py-2 border-b border-white/4 last:border-0">
                <div class="w-8 h-8 rounded-lg gradient-brand flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                    {{ substr($review->customer->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-xs font-semibold text-white">{{ $review->customer->name }}</p>
                        <div class="flex gap-0.5">
                            @for($i=1; $i<=5; $i++)
                            <svg class="w-3 h-3 {{ $i<=$review->rating ? 'text-yellow-400':'text-[#32324a]' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                        </div>
                    </div>
                    @if($review->comment)
                    <p class="text-xs text-[#a0a0c0] mt-0.5 truncate">{{ $review->comment }}</p>
                    @endif
                </div>
                <span class="text-[10px] text-[#7070a0] flex-shrink-0">{{ $review->created_at->diffForHumans() }}</span>
            </div>
            @empty
            <p class="text-sm text-[#7070a0] py-4 text-center">Henüz yorum yok</p>
            @endforelse
        </div>
    </div>

    {{-- Hızlı Aksiyonlar (2/5) --}}
    <div class="xl:col-span-2 glass-card rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">Hızlı Aksiyonlar</h3>
        <div class="space-y-2">
            <a href="{{ route('admin.appointments.create') }}" class="flex items-center gap-3 p-3 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="text-sm font-medium text-purple-300">Yeni Randevu</span>
            </a>
            <a href="{{ route('admin.calendar.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-sm font-medium text-blue-300">Takvimi Aç</span>
            </a>
            <a href="{{ route('admin.staff.create') }}" class="flex items-center gap-3 p-3 rounded-xl bg-green-500/10 hover:bg-green-500/20 border border-green-500/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <span class="text-sm font-medium text-green-300">Personel Ekle</span>
            </a>
            <a href="{{ route('admin.finance.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-yellow-500/10 hover:bg-yellow-500/20 border border-yellow-500/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-sm font-medium text-yellow-300">Finans Raporu</span>
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const data = @json($revenueChart);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.date),
            datasets: [{
                label: 'Gelir (₺)',
                data: data.map(d => d.revenue),
                backgroundColor: 'rgba(217,70,239,0.3)',
                borderColor: '#d946ef',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#7070a0', font: { size: 11 } } },
                y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#7070a0', font: { size: 11 }, callback: v => v.toLocaleString('tr-TR') + '₺' } }
            }
        }
    });
});
</script>
@endpush
