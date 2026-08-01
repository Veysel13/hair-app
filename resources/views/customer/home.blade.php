@extends('layouts.customer')
@section('title', 'Güzellik & Bakım Randevusu')

@push('head')
<script type="application/ld+json">
{
  "{{ '@' }}context": "https://schema.org",
  "{{ '@' }}graph": [
    {
      "{{ '@' }}type": "WebSite",
      "{{ '@' }}id": "{{ url('/') }}#website",
      "url": "{{ url('/') }}",
      "name": "Salonim",
      "description": "Güzellik & Bakım Randevu Platformu",
      "inLanguage": "tr-TR",
      "potentialAction": {
        "{{ '@' }}type": "SearchAction",
        "target": "{{ route('search') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    },
    {
      "{{ '@' }}type": "Organization",
      "{{ '@' }}id": "{{ url('/') }}#organization",
      "name": "Salonim",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('icon-512.png') }}",
      "sameAs": [
        "https://www.instagram.com/salonimcomtr"
      ]
    }
  ]
}
</script>
@endpush

@section('content')
<div class="space-y-8 pb-10">

    {{-- ── 1. DINAMIK HERO BANNER CAROUSEL (SLIDER) ── --}}
    <div x-data="{
        activeSlide: 0,
        slidesCount: 3,
        autoPlayInterval: null,
        init() {
            this.autoPlay();
        },
        autoPlay() {
            this.autoPlayInterval = setInterval(() => {
                this.next();
            }, 6000);
        },
        next() {
            this.activeSlide = (this.activeSlide + 1) % this.slidesCount;
        },
        prev() {
            this.activeSlide = (this.activeSlide - 1 + this.slidesCount) % this.slidesCount;
        },
        goTo(index) {
            this.activeSlide = index;
            clearInterval(this.autoPlayInterval);
            this.autoPlay();
        }
    }" class="relative rounded-3xl overflow-hidden shadow-xl shadow-purple-500/10 border border-white/5 h-[280px] sm:h-[320px] md:h-[360px]">
        
        {{-- SLIDE 1: Müsteri Randevu Odaklı --}}
        <div x-show="activeSlide === 0" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 -translate-x-8"
             class="absolute inset-0 bg-gradient-to-br from-[#86198f] via-[#6b21a8] to-[#3b0764] p-6 sm:p-10 flex flex-col justify-between">
            <div class="absolute right-6 bottom-2 opacity-15 pointer-events-none select-none">
                <span class="text-[140px] sm:text-[180px] md:text-[220px] font-bold">💇‍♀️</span>
            </div>
            <div class="relative z-10 space-y-3 sm:space-y-4">
                <span class="inline-flex items-center gap-1 bg-white/10 text-white text-[9px] sm:text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full backdrop-blur-md">
                    ⚡ ANINDA REZERVASYON
                </span>
                <h2 class="text-xl sm:text-3xl font-extrabold text-white leading-tight max-w-sm sm:max-w-md">
                    Güzellik ve Bakım Randevun Tek Tıkla!
                </h2>
                <p class="text-xs sm:text-sm text-white/80 max-w-xs sm:max-w-sm leading-relaxed">
                    Yakınındaki en iyi salonları keşfet, müsait saatini seç ve anında randevunu al.
                </p>
            </div>
            <div class="relative z-10 pt-2 flex items-center gap-3">
                <a href="{{ route('search') }}" class="btn bg-white text-[#86198f] hover:bg-purple-50 font-bold px-6 py-2.5 text-xs sm:text-sm shadow-lg rounded-xl transition-all hover:scale-[1.03]">
                    Hemen Keşfet
                </a>
            </div>
        </div>

        {{-- SLIDE 2: Isletme Tanıtım Odaklı --}}
        <div x-show="activeSlide === 1" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 -translate-x-8"
             class="absolute inset-0 bg-gradient-to-br from-[#db2777] via-[#9d174d] to-[#4c0519] p-6 sm:p-10 flex flex-col justify-between" style="display: none;">
            <div class="absolute right-6 bottom-2 opacity-15 pointer-events-none select-none">
                <span class="text-[140px] sm:text-[180px] md:text-[220px] font-bold">💈</span>
            </div>
            <div class="relative z-10 space-y-3 sm:space-y-4">
                <span class="inline-flex items-center gap-1 bg-white/10 text-white text-[9px] sm:text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full backdrop-blur-md">
                    💼 İŞLETMELERE ÖZEL
                </span>
                <h2 class="text-xl sm:text-3xl font-extrabold text-white leading-tight max-w-sm sm:max-w-md">
                    Salonunuzu Dijital Güce Dönüştürün!
                </h2>
                <p class="text-xs sm:text-sm text-white/80 max-w-xs sm:max-w-sm leading-relaxed">
                    Akıllı takvim, personel takibi, iptal önleyici kapora ve CRM çözümleri tek panelde.
                </p>
            </div>
            <div class="relative z-10 pt-2">
                <a href="{{ route('register.business') }}" class="btn bg-white text-[#db2777] hover:bg-pink-50 font-bold px-6 py-2.5 text-xs sm:text-sm shadow-lg rounded-xl transition-all hover:scale-[1.03]">
                    Salonunu Kaydet
                </a>
            </div>
        </div>

        {{-- SLIDE 3: Akıllı Randevu Motoru Tanıtımı --}}
        <div x-show="activeSlide === 2" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 -translate-x-8"
             class="absolute inset-0 bg-gradient-to-br from-[#4f46e5] via-[#3730a3] to-[#1e1b4b] p-6 sm:p-10 flex flex-col justify-between" style="display: none;">
            <div class="absolute right-6 bottom-2 opacity-15 pointer-events-none select-none">
                <span class="text-[140px] sm:text-[180px] md:text-[220px] font-bold">📆</span>
            </div>
            <div class="relative z-10 space-y-3 sm:space-y-4">
                <span class="inline-flex items-center gap-1 bg-white/10 text-white text-[9px] sm:text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full backdrop-blur-md">
                    ⚙️ AKILLI MOTOR
                </span>
                <h2 class="text-xl sm:text-3xl font-extrabold text-white leading-tight max-w-sm sm:max-w-md">
                    Sıfır Çakışma, Maksimum Verim
                </h2>
                <p class="text-xs sm:text-sm text-white/80 max-w-xs sm:max-w-sm leading-relaxed">
                    Sistem, seçtiğiniz hizmetlerin toplam süresini hesaplayarak personelin sadece uygun saatlerini açar.
                </p>
            </div>
            <div class="relative z-10 pt-2">
                <button @click="document.getElementById('how-it-works-section').scrollIntoView({ behavior: 'smooth' })" 
                        class="btn bg-white text-[#4f46e5] hover:bg-indigo-50 font-bold px-6 py-2.5 text-xs sm:text-sm shadow-lg rounded-xl transition-all hover:scale-[1.03]">
                    Nasıl Çalışır? 🔍
                </button>
            </div>
        </div>

        {{-- Slider Yön Okları --}}
        <button type="button" @click="prev()" aria-label="Önceki Slayt" class="absolute left-2 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-black/25 hover:bg-black/50 text-white flex items-center justify-center transition-colors text-lg font-bold">
            ‹
        </button>
        <button type="button" @click="next()" aria-label="Sonraki Slayt" class="absolute right-2 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-black/25 hover:bg-black/50 text-white flex items-center justify-center transition-colors text-lg font-bold">
            ›
        </button>

        {{-- Slider Göstergeleri (Dots) --}}
        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex gap-2">
            <template x-for="(item, index) in Array.from({length: slidesCount})">
                <button type="button" @click="goTo(index)" 
                        :aria-label="'Slayt ' + (index + 1)"
                        :class="activeSlide === index ? 'w-6 bg-white' : 'w-2 bg-white/40'" 
                        class="h-2 rounded-full transition-all duration-300 p-2 border-0"></button>
            </template>
        </div>
    </div>

    {{-- ── 2. HIZLI ARAMA KATEGORİLERİ ── --}}
    <div>
        <h3 class="text-sm font-bold text-[#a0a0c0] uppercase tracking-wider mb-4">Hangi Hizmeti Arıyorsun?</h3>
        <div class="grid grid-cols-3 gap-3.5">
            <a href="{{ route('search', ['category' => 'hair_salon']) }}" class="glass-card rounded-2xl p-4 text-center hover:border-purple-500/30 transition-all hover:scale-[1.03] group">
                <div class="text-4xl mb-2 transition-transform duration-300 group-hover:scale-110">💇‍♀️</div>
                <p class="text-sm font-bold text-white">Kuaför</p>
                <p class="text-xs text-[#a0a0c0] mt-1">Kadın & Erkek</p>
            </a>
            <a href="{{ route('search', ['category' => 'barber']) }}" class="glass-card rounded-2xl p-4 text-center hover:border-purple-500/30 transition-all hover:scale-[1.03] group">
                <div class="text-4xl mb-2 transition-transform duration-300 group-hover:scale-110">💈</div>
                <p class="text-sm font-bold text-white">Berber</p>
                <p class="text-xs text-[#a0a0c0] mt-1">Erkek Saç Sakal</p>
            </a>
            <a href="{{ route('search', ['category' => 'nail_salon']) }}" class="glass-card rounded-2xl p-4 text-center hover:border-purple-500/30 transition-all hover:scale-[1.03] group">
                <div class="text-4xl mb-2 transition-transform duration-300 group-hover:scale-110">💅</div>
                <p class="text-sm font-bold text-white">Tırnak Salonu</p>
                <p class="text-xs text-[#a0a0c0] mt-1">Protez, Oje</p>
            </a>
        </div>
    </div>

    {{-- ── 3. PLATFORM ISTATISTIKLERI (STATS BAR) ── --}}
    <div class="glass-card rounded-2xl p-5 border border-white/5 bg-white/[0.02]">
        <div class="grid grid-cols-3 divide-x divide-white/5">
            <div class="text-center">
                <p class="text-2xl font-black gradient-text">4.9</p>
                <p class="text-[11px] text-[#a0a0c0] font-bold uppercase tracking-wider mt-1">Müşteri Puanı</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-black gradient-text">5.000+</p>
                <p class="text-[11px] text-[#a0a0c0] font-bold uppercase tracking-wider mt-1">Aktif Randevu</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-black gradient-text">350+</p>
                <p class="text-[11px] text-[#a0a0c0] font-bold uppercase tracking-wider mt-1">Seçkin Salon</p>
            </div>
        </div>
    </div>

    {{-- ── 4. "NASIL ÇALIŞIR?" BÖLÜMÜ (ALT ALTA BÖLÜMLER) ── --}}
    <div id="how-it-works-section" class="space-y-8">
        {{-- Müşteriler İçin Kısım --}}
        <div class="space-y-4">
            <div class="flex items-center gap-2 border-l-4 border-[#e879f9] pl-3">
                <h3 class="text-sm font-extrabold text-[#e879f9] uppercase tracking-wider">Müşteriler İçin</h3>
                <span class="text-sm font-bold text-white">— 3 Adımda Kolay Randevu</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="glass-card rounded-2xl p-5 space-y-3 border border-white/5 hover:border-purple-500/10 transition-all">
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-sm font-bold text-[#e879f9]">1</div>
                    <h4 class="text-sm font-bold text-white">Yakınındaki Salonları Keşfet</h4>
                    <p class="text-xs text-[#a0a0c0] leading-relaxed">
                        Konum servislerini kullanarak yakınındaki en popüler kuaför, berber ve tırnak salonlarını gör. Puan ve yorumları incele.
                    </p>
                </div>
                <div class="glass-card rounded-2xl p-5 space-y-3 border border-white/5 hover:border-purple-500/10 transition-all">
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-sm font-bold text-[#e879f9]">2</div>
                    <h4 class="text-sm font-bold text-white">İşlemini ve Personeli Seç</h4>
                    <p class="text-xs text-[#a0a0c0] leading-relaxed">
                        Yaptırmak istediğin saç kesimi, fön veya tırnak bakımı gibi işlemleri seç. Portfolyosunu beğendiğin uzmanı belirle.
                    </p>
                </div>
                <div class="glass-card rounded-2xl p-5 space-y-3 border border-white/5 hover:border-purple-500/10 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center text-lg font-bold text-[#e879f9]">3</div>
                    <h4 class="text-base font-bold text-white">Müsait Saatte Randevunu Kilitle</h4>
                    <p class="text-sm text-[#a0a0c0] leading-relaxed">
                        Akıllı takvimden uygun saati seç. Ön ödeme (kapora) ile yerini güvenceye al. Randevun anında hazır olsun!
                    </p>
                </div>
            </div>
        </div>

        {{-- İşletmeler İçin Kısım --}}
        <div class="space-y-4">
            <div class="flex items-center gap-2 border-l-4 border-[#f472b6] pl-3">
                <h3 class="text-base font-extrabold text-[#f472b6] uppercase tracking-wider">İşletmeler İçin</h3>
                <span class="text-base font-bold text-white">— 3 Adımda Akıllı Yönetim</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="glass-card rounded-2xl p-5 space-y-3 border border-white/5 hover:border-pink-500/10 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-pink-500/10 flex items-center justify-center text-lg font-bold text-[#f472b6]">1</div>
                    <h4 class="text-base font-bold text-white">Salon Profilini Tanımla</h4>
                    <p class="text-sm text-[#a0a0c0] leading-relaxed">
                        Hizmet kategorilerini, fiyatlarını, çalışan listesini ve çalışma saatlerini birkaç dakikada sisteme gir.
                    </p>
                </div>
                <div class="glass-card rounded-2xl p-5 space-y-3 border border-white/5 hover:border-pink-500/10 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-pink-500/10 flex items-center justify-center text-lg font-bold text-[#f472b6]">2</div>
                    <h4 class="text-base font-bold text-white">Randevuları Akıllı Yönet</h4>
                    <p class="text-sm text-[#a0a0c0] leading-relaxed">
                        Müşterilerinizin seçtiği işlemlerin sürelerine göre çalışan takvimlerinde otomatik müsait saatler açılsın, çakışmalar son bulsun.
                    </p>
                </div>
                <div class="glass-card rounded-2xl p-5 space-y-3 border border-white/5 hover:border-pink-500/10 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-pink-500/10 flex items-center justify-center text-lg font-bold text-[#f472b6]">3</div>
                    <h4 class="text-base font-bold text-white">Finans ve CRM ile Ölçekle</h4>
                    <p class="text-sm text-[#a0a0c0] leading-relaxed">
                        Giderlerinizi takip edin, envanterdeki kritik stokları denetleyin. Otomatik hatırlatıcılar ile müşteri sadakatini artırın.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── 5. POPÜLER VE YÜKSEK PUANLI SALONLAR (FEATURED) ── --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-[#a0a0c0] uppercase tracking-wider">Popüler İşletmeler</h3>
            <a href="{{ route('search') }}" class="text-xs text-[#e879f9] hover:underline font-bold">Tümünü Gör →</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($featuredBusinesses as $business)
            <div class="glass-card rounded-2xl p-5 flex gap-4 hover:border-purple-500/20 transition-all border border-transparent">
                {{-- Logo or Initial --}}
                <div class="w-18 h-18 rounded-xl bg-gradient-to-br from-[#d946ef] to-[#6366f1] flex items-center justify-center text-white font-black text-xl flex-shrink-0 shadow-lg shadow-purple-500/5">
                    {{ substr($business->name, 0, 1) }}
                </div>
                
                <div class="flex-1 min-w-0 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1.5 justify-between">
                            <h4 class="text-base font-bold text-white truncate">{{ $business->name }}</h4>
                            <div class="flex items-center gap-1 flex-shrink-0 text-yellow-400 font-extrabold text-xs bg-yellow-400/5 px-2 py-0.5 rounded-lg border border-yellow-400/10">
                                ⭐️ <span>{{ number_format($business->avg_rating, 1) }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-[#a0a0c0] flex items-center gap-1 mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate">{{ $business->district }}, {{ $business->city }}</span>
                        </p>
                        <p class="text-xs text-[#a0a0c0] mt-2 line-clamp-1 leading-relaxed">{{ $business->description ?? 'Harika saç ve tırnak tasarımları için ziyaret edin.' }}</p>
                    </div>

                    <div class="flex items-center justify-between mt-4 gap-2">
                        <span class="badge badge-purple text-[10px] uppercase tracking-wider font-extrabold px-2.5 py-1">
                            {{ match($business->category) { 'hair_salon'=>'Kuaför', 'barber'=>'Berber', 'nail_salon'=>'Tırnak Salonu', default=>$business->category } }}
                        </span>
                        
                        <a href="{{ route('book.show', $business) }}" class="btn btn-primary py-2 px-5 text-xs font-extrabold transition-all hover:scale-[1.03] shadow-md shadow-purple-500/10 rounded-xl">
                            Randevu Al
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-[#a0a0c0] text-sm glass-card rounded-2xl md:col-span-2">
                Öne çıkan işletme bulunamadı.
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── 6. İŞLETME KAYIT ÇAĞRISI (CTA BANNER) ── --}}
    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-[#db2777]/20 via-[#9d174d]/10 to-transparent p-7 border border-pink-500/10 text-center space-y-5">
        <div class="space-y-2.5">
            <h3 class="text-base font-extrabold text-white">İşletmenizi Salonim ile Büyütün!</h3>
            <p class="text-sm text-[#a0a0c0] max-w-sm mx-auto leading-relaxed">
                Kuaför, berber veya tırnak salonunuzu sisteme entegre edin. Randevu çakışmalarını sıfıra indirin, müşteri kitlelerinizi yönetin.
            </p>
        </div>
        <div class="pt-1 flex flex-col sm:flex-row items-center justify-center gap-3.5">
            <a href="{{ route('register.business') }}" class="btn btn-secondary text-xs sm:text-sm px-6 py-3 hover:bg-white/10 transition-all font-bold rounded-xl border border-white/10 text-white">
                Salon Sahibi Olarak Başla 🚀
            </a>
            <a href="{{ route('login') }}" class="text-xs sm:text-sm text-[#a0a0c0] hover:text-white transition-colors font-semibold py-2">
                Zaten üye misiniz? Giriş Yapın 💼
            </a>
        </div>
    </div>

    {{-- ── 7. PWA UYGULAMA YÜKLEME ALANI (Sadece kurulmadıysa görünür) ── --}}
    <div id="pwa-homepage-promo" class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-purple-900/20 to-indigo-950/20 p-7 border border-purple-500/10 text-center space-y-5" style="display: none;">
        <div class="space-y-2.5">
            <h3 class="text-base font-extrabold text-white flex items-center justify-center gap-2">
                <span>📲</span> Salonim Uygulamasını İndirin
            </h3>
            <p id="pwa-promo-desc" class="text-sm text-[#a0a0c0] max-w-md mx-auto leading-relaxed">
                Randevularınızı saniyeler içinde almak, anlık bildirimleri takip etmek ve hızlı erişim sağlamak için hemen kurun.
            </p>
        </div>
        <div id="pwa-promo-action" class="pt-1 flex items-center justify-center">
            <button onclick="window.triggerPwaInstall()" class="btn btn-primary text-xs sm:text-sm px-6 py-3 font-bold rounded-xl hover:scale-[1.03] transition-all shadow-lg shadow-purple-500/10">
                Uygulamayı Kur ⚡
            </button>
        </div>
    </div>

</div>
@endsection
