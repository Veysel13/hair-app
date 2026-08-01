@extends('layouts.customer')
@section('title', 'İşletme Arama')

@push('head')
<script type="application/ld+json">
{
  "{{ '@' }}context": "https://schema.org",
  "{{ '@' }}type": "ItemList",
  "name": "Salonim İşletmeler ve Hizmet Arama",
  "numberOfItems": {{ $businesses->count() }},
  "itemListElement": [
    @foreach($businesses->take(10) as $index => $b)
    {
      "{{ '@' }}type": "ListItem",
      "position": {{ $index + 1 }},
      "item": {
        "{{ '@' }}type": "BeautySalon",
        "name": "{{ addslashes($b->name) }}",
        "url": "{{ route('book.show', $b) }}",
        "address": {
          "{{ '@' }}type": "PostalAddress",
          "addressLocality": "{{ addslashes($b->district) }}",
          "addressRegion": "{{ addslashes($b->city) }}",
          "addressCountry": "TR"
        }
      }
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endpush

@section('content')
<div class="space-y-4" x-data="{
    lat: {{ $lat }},
    lng: {{ $lng }},
    getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                this.lat = position.coords.latitude;
                this.lng = position.coords.longitude;
                // Formu güncelleyip tekrar gönder
                document.getElementById('latitude_input').value = this.lat;
                document.getElementById('longitude_input').value = this.lng;
                document.getElementById('search_form').submit();
            }, error => {
                alert('Konum alınamadı. Lütfen tarayıcı izinlerini kontrol edin.');
            });
        } else {
            alert('Tarayıcınız konum bilgisini desteklemiyor.');
        }
    }
}">

    {{-- Arama ve Filtre Paneli --}}
    <div class="glass-card rounded-2xl p-5">
        <form method="GET" id="search_form" class="space-y-3">
            <input type="hidden" name="latitude" id="latitude_input" :value="lat">
            <input type="hidden" name="longitude" id="longitude_input" :value="lng">

            <div class="relative">
                <input type="text" name="q" value="{{ request('q') }}" class="form-input" style="padding-left: 2.5rem;" placeholder="İşletme adı veya konum ara...">
                <svg class="w-5 h-5 text-[#7070a0] absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="form-label text-[10px]">Kategori</label>
                    <select name="category" class="form-input text-xs" onchange="this.form.submit()">
                        <option value="">Tüm Kategoriler</option>
                        <option value="hair_salon" {{ request('category') === 'hair_salon' ? 'selected' : '' }}>💇‍♀️ Kuaför</option>
                        <option value="barber" {{ request('category') === 'barber' ? 'selected' : '' }}>💈 Berber</option>
                        <option value="nail_salon" {{ request('category') === 'nail_salon' ? 'selected' : '' }}>💅 Tırnak</option>
                    </select>
                </div>
                <div>
                    <label class="form-label text-[10px]">Sıralama</label>
                    <select name="sort" class="form-input text-xs" onchange="this.form.submit()">
                        <option value="distance" {{ request('sort') === 'distance' ? 'selected' : '' }}>📍 Yakınlık (Mesafe)</option>
                        <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>⭐️ Puan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="form-label text-[10px]">Tarih (Müsaitlik)</label>
                    <input type="date" name="date" value="{{ request('date') }}" min="{{ now()->format('Y-m-d') }}" class="form-input text-xs" onchange="this.form.submit()">
                </div>
                <div>
                    <label class="form-label text-[10px]">Müsait Saat</label>
                    <input type="time" name="time" value="{{ request('time') }}" class="form-input text-xs" onchange="this.form.submit()">
                </div>
            </div>

            <div class="pt-2 flex gap-2">
                <button type="button" @click="getLocation()" class="btn btn-secondary text-xs py-2 px-3 flex-1 flex items-center justify-center gap-1">
                    📍 Konumumu Kullan
                </button>
                <button type="submit" class="btn btn-primary text-xs py-2 px-6">
                    Filtrele
                </button>
            </div>
        </form>
    </div>

    {{-- Arama Sonuçları --}}
    <div class="space-y-4">
        <h3 class="text-sm font-semibold text-white px-1">Bulunan İşletmeler ({{ $businesses->count() }})</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($businesses as $business)
            <div class="glass-card rounded-2xl p-4 flex gap-4 hover:border-purple-500/20 transition-all border border-transparent">
                <div class="w-16 h-16 rounded-xl gradient-brand flex items-center justify-center text-white font-extrabold text-lg flex-shrink-0">
                    {{ substr($business->name, 0, 1) }}
                </div>
                
                <div class="flex-1 min-w-0 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1.5 justify-between">
                            <h4 class="text-sm font-bold text-white truncate">{{ $business->name }}</h4>
                            <div class="flex items-center gap-0.5 flex-shrink-0 text-yellow-400 font-semibold text-xs">
                                ⭐️ <span>{{ number_format($business->avg_rating, 1) }}</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-[#a0a0c0] truncate mt-0.5">{{ $business->district }}, {{ $business->city }}</p>
                        <p class="text-[10px] text-purple-400 mt-1 font-semibold flex items-center gap-1">
                            📍 <span>{{ number_format($business->distance, 1, ',', '.') }} km uzakta</span>
                        </p>
                    </div>

                    <div class="flex items-center justify-between mt-3 gap-2">
                        <span class="badge badge-purple text-[10px]">
                            {{ match($business->category) { 'hair_salon'=>'Kuaför', 'barber'=>'Berber', 'nail_salon'=>'Tırnak Salonu', default=>$business->category } }}
                        </span>
                        
                        <a href="{{ route('book.show', $business) }}" class="btn btn-primary btn-sm py-1.5 px-4 text-xs">
                            Randevu Al
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="glass-card rounded-2xl p-8 text-center text-[#7070a0] md:col-span-2">
                Kriterlerinize uygun işletme bulunamadı. Lütfen aramayı veya filtreleri değiştirin.
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
