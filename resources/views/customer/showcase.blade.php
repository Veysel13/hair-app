@extends('layouts.customer')
@section('title', $business->name . ' — Saç & Güzellik Randevusu')

@push('head')
<meta name="description" content="{{ Str::limit($business->description ?? $business->name . ' salonumuzda saç tasarımı, kesim, boya ve bakım hizmetlerimizden randevu alın.', 150) }}">
<meta property="og:title" content="{{ $business->name }} — Salonim">
<meta property="og:description" content="{{ Str::limit($business->description, 150) }}">
@if($business->cover_image)
<meta property="og:image" content="{{ asset($business->cover_image) }}">
@endif
<meta property="og:type" content="business.business">

{{-- LocalBusiness Schema.org Structured Data for SEO --}}
<script type="application/ld+json">
{
  "{{ '@' }}context": "https://schema.org",
  "{{ '@' }}type": "BeautySalon",
  "name": "{{ $business->name }}",
  "image": "{{ $business->logo ? asset($business->logo) : asset('default-logo.png') }}",
  "{{ '@' }}id": "{{ url()->current() }}",
  "url": "{{ url()->current() }}",
  "telephone": "{{ $business->phone }}",
  "address": {
    "{{ '@' }}type": "PostalAddress",
    "streetAddress": "{{ $business->address }}",
    "addressLocality": "{{ $business->district }}",
    "addressRegion": "{{ $business->city }}",
    "addressCountry": "TR"
  },
  "geo": {
    "{{ '@' }}type": "GeoCoordinates",
    "latitude": "{{ $business->latitude ?? '41.0082' }}",
    "longitude": "{{ $business->longitude ?? '28.9784' }}"
  },
  "openingHoursSpecification": [
    @foreach($business->hours as $h)
    {
      "{{ '@' }}type": "OpeningHoursSpecification",
      "dayOfWeek": "{{ match($h->day_of_week) { 1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 0=>'Sunday' } }}",
      "opens": "{{ $h->is_open ? substr($h->open_time, 0, 5) : '00:00' }}",
      "closes": "{{ $h->is_open ? substr($h->close_time, 0, 5) : '00:00' }}"
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endpush

@section('content')
<div class="space-y-8">

    {{-- 1. HERO HEADER SECTION --}}
    <div class="relative rounded-3xl overflow-hidden border border-white/5 bg-[#121218] shadow-2xl">
        {{-- Cover Image --}}
        <div class="h-64 sm:h-80 md:h-96 w-full relative">
            @if($business->cover_image)
            <img src="{{ asset($business->cover_image) }}" alt="{{ $business->name }}" class="w-full h-full object-cover">
            @else
            <div class="w-full h-full gradient-brand opacity-20"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-[#0f0f14] via-[#0f0f14]/40 to-transparent"></div>
        </div>

        {{-- Brand Info Overlay --}}
        <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8 flex flex-col sm:flex-row sm:items-end justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-6">
                {{-- Logo --}}
                <div class="w-20 h-20 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-[#121218] border-2 border-purple-500/30 shadow-xl flex-shrink-0">
                    @if($business->logo)
                    <img src="{{ asset($business->logo) }}" alt="{{ $business->name }} Logo" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full gradient-brand flex items-center justify-center text-white text-3xl font-black">
                        {{ substr($business->name, 0, 1) }}
                    </div>
                    @endif
                </div>

                {{-- Name & Status --}}
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl sm:text-3xl font-extrabold text-white tracking-tight">{{ $business->name }}</h1>
                        <span class="badge badge-purple text-[10px] sm:text-xs">
                            {{ match($business->category) {
                                'hair_salon' => 'Kuaför',
                                'barber' => 'Berber',
                                'nail_salon' => 'Tırnak Salonu',
                                'spa' => 'Spa',
                                'beauty_center' => 'Güzellik Merkezi',
                                default => 'Salon'
                            } }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-[#a0a0c0] flex items-center gap-1">
                        📍 <span>{{ $business->address }}, {{ $business->district }} / {{ $business->city }}</span>
                    </p>
                    <div class="flex items-center gap-3 text-xs sm:text-sm">
                        <div class="flex items-center gap-1 text-yellow-400 font-bold">
                            ★ <span>{{ number_format($business->avg_rating ?? 5.0, 1) }}</span>
                            <span class="text-[#7070a0] font-normal">({{ $business->total_reviews ?? 0 }} yorum)</span>
                        </div>
                        <span class="text-white/10">|</span>
                        @if($business->isOpen())
                        <span class="text-green-400 font-bold flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span> Şimdi Açık
                        </span>
                        @else
                        <span class="text-red-400 font-bold flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-red-400"></span> Kapalı
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Action Button --}}
            <div class="flex-shrink-0">
                <a href="{{ route('book.show', $business) }}" class="btn btn-primary btn-lg px-8 py-3.5 text-sm font-bold shadow-lg shadow-purple-500/20 w-full sm:w-auto text-center flex items-center justify-center gap-2 transition-all hover:scale-105">
                    Randevu Al ⚡
                </a>
            </div>
        </div>
    </div>

    {{-- 2. DYNAMIC GRID LAYOUT (Main content & Sidebar) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Sol/Orta Alan: Hizmetler, Galeri, Uzmanlar & Yorumlar --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Açıklama --}}
            @if($business->description)
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>✨</span> Hakkımızda
                </h3>
                <p class="text-sm text-[#a0a0c0] leading-relaxed">{{ $business->description }}</p>
            </div>
            @endif

            {{-- Hizmetler --}}
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>💇‍♀️</span> Hizmet Menümüz
                </h3>

                @php $groupedServices = $business->activeServices->groupBy(fn($s) => $s->category?->name ?? 'Genel') @endphp

                @forelse($groupedServices as $catName => $catServices)
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-[#7070a0] uppercase tracking-wider">{{ $catName }}</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($catServices as $s)
                        <div class="p-4 rounded-2xl bg-white/3 border border-white/5 flex items-center justify-between gap-3 hover:border-purple-500/20 transition-all">
                            <div>
                                <h5 class="text-xs font-bold text-white">{{ $s->name }}</h5>
                                <p class="text-[10px] text-[#7070a0] mt-0.5">⏱️ {{ $s->duration_minutes }} dk</p>
                            </div>
                            <div class="text-right">
                                @if(isset($s->original_price) && $s->original_price > $s->price)
                                <span class="text-[9px] text-[#7070a0] line-through block">{{ number_format($s->original_price, 0, ',', '.') }} ₺</span>
                                <span class="text-xs font-bold text-green-400 block">{{ number_format($s->price, 0, ',', '.') }} ₺</span>
                                <span class="text-[8px] bg-green-500/10 text-green-400 font-bold px-1.5 py-0.5 rounded-md mt-0.5 inline-block">{{ $s->discount_label }}</span>
                                @else
                                <span class="text-xs font-bold text-[#e879f9]">{{ number_format($s->price, 0, ',', '.') }} ₺</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-[#7070a0] text-sm">Hizmet bilgisi bulunamadı.</div>
                @endforelse
            </div>

            {{-- Galeri --}}
            @if($business->images->isNotEmpty())
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>🖼️</span> Galeri & Çalışmalarımız
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($business->images as $img)
                    <div class="group relative aspect-square rounded-2xl overflow-hidden border border-white/5 bg-[#121218]">
                        <img src="{{ asset($img->image_path) }}" alt="{{ $img->caption ?? 'Gallery Image' }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        @if($img->caption)
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-3 pt-6 opacity-0 group-hover:opacity-100 transition-opacity">
                            <p class="text-[10px] text-white font-medium truncate">{{ $img->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Uzman Personel --}}
            @if($business->activeStaff->isNotEmpty())
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>🤝</span> Uzman Kadromuz
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($business->activeStaff as $member)
                    <div class="p-4 rounded-2xl bg-white/3 border border-white/5 flex flex-col items-center text-center space-y-3">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-[#121218] border-2 border-purple-500/10 flex items-center justify-center text-white text-xl font-bold">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white">{{ $member->name }}</h4>
                            <p class="text-[9px] text-[#7070a0] uppercase font-semibold mt-0.5">{{ $member->title ?? 'Uzman' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Aktif Kampanyalar --}}
            @if($business->activeCampaigns->isNotEmpty())
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>🔥</span> Aktif Kampanyalar & Fırsatlar
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($business->activeCampaigns as $camp)
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-purple-500/10 to-pink-500/5 border border-purple-500/20 space-y-3 relative overflow-hidden group">
                        {{-- Background Glow --}}
                        <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>

                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="badge badge-purple text-[9px] font-bold uppercase tracking-wider px-2 py-0.5">
                                    {{ $camp->discount_type === 'percentage' ? '%' . number_format($camp->discount_value, 0) . ' İndirim' : number_format($camp->discount_value, 0) . ' ₺ İndirim' }}
                                </span>
                                <h4 class="text-sm font-extrabold text-white mt-1.5">{{ $camp->name }}</h4>
                            </div>
                            @if($camp->code)
                            <div class="bg-white/5 border border-white/10 px-2.5 py-1 rounded-xl text-center">
                                <p class="text-[8px] text-[#7070a0] font-bold uppercase">KOD</p>
                                <p class="text-xs font-mono font-bold text-[#e879f9]">{{ $camp->code }}</p>
                            </div>
                            @endif
                        </div>

                        <p class="text-xs text-[#a0a0c0] leading-relaxed">{{ $camp->description }}</p>

                        @if($camp->ends_at)
                        <div class="pt-2 border-t border-white/5 flex items-center justify-between text-[10px] text-[#7070a0]">
                            <span>⏳ Son Geçerlilik:</span>
                            <span class="font-semibold text-white/70">{{ $camp->ends_at->format('d.m.Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Müşteri Yorumları --}}
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>⭐</span> Müşteri Yorumları
                </h3>

                @forelse($business->visibleReviews as $review)
                <div class="p-4 rounded-2xl bg-white/3 border border-white/5 space-y-3">
                    <div class="flex justify-between items-center gap-3">
                        <div>
                            <h5 class="text-xs font-bold text-white">{{ $review->customer->name }}</h5>
                            <p class="text-[9px] text-[#7070a0]">{{ $review->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="flex items-center gap-1 text-yellow-400 font-bold text-xs bg-yellow-400/5 px-2 py-1 rounded-lg border border-yellow-400/10">
                            ★ {{ $review->rating }}
                        </div>
                    </div>
                    
                    @if($review->comment)
                    <p class="text-xs text-[#a0a0c0] leading-relaxed italic">"{{ $review->comment }}"</p>
                    @endif

                    @if($review->business_reply)
                    <div class="bg-purple-950/20 border border-purple-500/10 p-3 rounded-xl mt-2 space-y-1">
                        <p class="text-[10px] font-bold text-[#e879f9] flex items-center gap-1">
                            <span>💬</span> Salonun Yanıtı:
                        </p>
                        <p class="text-xs text-[#a0a0c0] leading-relaxed">{{ $review->business_reply }}</p>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-12 text-[#7070a0] text-sm">
                    Henüz yorum yapılmamış. İlk yorum yapan siz olun!
                </div>
                @endforelse
            </div>

        </div>

        {{-- Sağ Alan: Çalışma Saatleri & İletişim Bilgileri --}}
        <div class="space-y-6">
            
            {{-- Çalışma Saatleri --}}
            <div class="glass-card rounded-3xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>📆</span> Çalışma Saatleri
                </h3>
                <div class="space-y-2">
                    @foreach($business->hours as $hour)
                    <div class="flex justify-between items-center text-xs py-1.5 border-b border-white/3 last:border-0">
                        <span class="text-[#a0a0c0]">
                            {{ match($hour->day_of_week) {
                                1 => 'Pazartesi',
                                2 => 'Salı',
                                3 => 'Çarşamba',
                                4 => 'Perşembe',
                                5 => 'Cuma',
                                6 => 'Cumartesi',
                                0 => 'Pazar'
                            } }}
                        </span>
                        @if($hour->is_open)
                        <span class="text-white font-semibold">{{ substr($hour->open_time, 0, 5) }} - {{ substr($hour->close_time, 0, 5) }}</span>
                        @else
                        <span class="text-red-400 font-bold">Kapalı</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- İletişim & Sosyal Medya --}}
            <div class="glass-card rounded-3xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>📞</span> İletişim Bilgileri
                </h3>
                
                <div class="space-y-3.5 text-xs">
                    {{-- Telefon --}}
                    <div class="flex items-start gap-3">
                        <span class="text-base">📱</span>
                        <div>
                            <p class="text-[#7070a0] text-[10px] uppercase font-bold">Telefon</p>
                            <a href="tel:{{ $business->phone }}" class="text-white hover:text-purple-400 font-bold transition-colors">{{ $business->phone }}</a>
                        </div>
                    </div>

                    {{-- E-posta --}}
                    @if($business->email)
                    <div class="flex items-start gap-3">
                        <span class="text-base">✉️</span>
                        <div>
                            <p class="text-[#7070a0] text-[10px] uppercase font-bold">E-posta</p>
                            <a href="mailto:{{ $business->email }}" class="text-white hover:text-purple-400 transition-colors">{{ $business->email }}</a>
                        </div>
                    </div>
                    @endif

                    {{-- Web sitesi --}}
                    @if($business->website)
                    <div class="flex items-start gap-3">
                        <span class="text-base">🌐</span>
                        <div>
                            <p class="text-[#7070a0] text-[10px] uppercase font-bold">Web Sitesi</p>
                            <a href="{{ $business->website }}" target="_blank" class="text-white hover:text-purple-400 transition-colors font-semibold truncate block max-w-[200px]">{{ $business->website }}</a>
                        </div>
                    </div>
                    @endif

                    {{-- Instagram --}}
                    @if($business->instagram)
                    <div class="flex items-start gap-3">
                        <span class="text-base">📸</span>
                        <div>
                            <p class="text-[#7070a0] text-[10px] uppercase font-bold">Instagram</p>
                            <a href="https://instagram.com/{{ $business->instagram }}" target="_blank" class="text-[#e879f9] hover:underline font-bold transition-all">{{ '@' . $business->instagram }}</a>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Hızlı Arama Butonu --}}
                <div class="pt-2">
                    <a href="tel:{{ $business->phone }}" class="w-full btn btn-secondary text-xs font-semibold py-2.5 flex items-center justify-center gap-1.5">
                        📞 Hemen Ara
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
