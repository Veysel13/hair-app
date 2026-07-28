@extends('layouts.admin')
@section('title', 'Salon Ayarları')
@section('page-title', 'Salon Ayarları')
@section('page-subtitle', 'Salon bilgileri, çalışma saatleri ve galeri yönetimi')

@section('content')
<div x-data="{ activeTab: 'general' }" class="space-y-6">

    {{-- Durum Bildirimleri --}}
    @if(session('success'))
    <div class="bg-green-500/10 border border-green-500/30 text-green-400 text-sm rounded-xl px-4 py-3">
        {{ session('success') }}
    </div>
    @endif

    {{-- Tab Menüsü --}}
    <div class="flex border-b border-white/5 gap-2">
        <button @click="activeTab = 'general'" 
                :class="activeTab === 'general' ? 'border-purple-500 text-white font-semibold' : 'border-transparent text-[#7070a0] hover:text-white'"
                class="pb-3 px-4 border-b-2 text-sm transition-all focus:outline-none">
            👤 Genel Bilgiler
        </button>
        <button @click="activeTab = 'hours'" 
                :class="activeTab === 'hours' ? 'border-purple-500 text-white font-semibold' : 'border-transparent text-[#7070a0] hover:text-white'"
                class="pb-3 px-4 border-b-2 text-sm transition-all focus:outline-none">
            📆 Çalışma Saatleri
        </button>
        <button @click="activeTab = 'gallery'" 
                :class="activeTab === 'gallery' ? 'border-purple-500 text-white font-semibold' : 'border-transparent text-[#7070a0] hover:text-white'"
                class="pb-3 px-4 border-b-2 text-sm transition-all focus:outline-none">
            🖼️ Görsel Galerisi
        </button>
    </div>

    {{-- ── 1. GENEL BİLGİLER TAB ── --}}
    <div x-show="activeTab === 'general'" x-transition.opacity class="space-y-4">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Sol Kolon: Ana Detaylar --}}
                <div class="lg:col-span-2 space-y-4">
                    <div class="glass-card rounded-2xl p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-white">Genel Salon Detayları</h3>
                        
                        <div>
                            <label class="form-label">Salon Adı *</label>
                            <input type="text" name="name" value="{{ old('name', $business->name) }}" class="form-input" required>
                            <div class="mt-1.5 flex items-center justify-between text-[11px] text-[#7070a0]">
                                <span>
                                    🔗 Tanıtım Sayfası Linki: <span class="text-white font-mono font-medium">/isletme/{{ $business->slug }}</span>
                                </span>
                                <a href="{{ route('business.showcase', $business->slug) }}" target="_blank" class="text-purple-400 hover:text-purple-300 font-bold flex items-center gap-1 transition-colors">
                                    👁️ Sayfayı Önizle
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Salon Kategorisi *</label>
                                <select name="category" class="form-input" required>
                                    <option value="hair_salon" {{ old('category', $business->category) === 'hair_salon' ? 'selected' : '' }}>Kuaför</option>
                                    <option value="barber" {{ old('category', $business->category) === 'barber' ? 'selected' : '' }}>Berber</option>
                                    <option value="nail_salon" {{ old('category', $business->category) === 'nail_salon' ? 'selected' : '' }}>Tırnak Salonu</option>
                                    <option value="spa" {{ old('category', $business->category) === 'spa' ? 'selected' : '' }}>Spa</option>
                                    <option value="beauty_center" {{ old('category', $business->category) === 'beauty_center' ? 'selected' : '' }}>Güzellik Merkezi</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Telefon Numarası *</label>
                                <input type="text" name="phone" value="{{ old('phone', $business->phone) }}" class="form-input" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">E-posta Adresi</label>
                                <input type="email" name="email" value="{{ old('email', $business->email) }}" class="form-input" placeholder="salon@example.com">
                            </div>
                            <div>
                                <label class="form-label">Web Sitesi</label>
                                <input type="text" name="website" value="{{ old('website', $business->website) }}" class="form-input" placeholder="https://example.com">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Instagram Adresi</label>
                            <div class="flex rounded-lg overflow-hidden border border-white/10 focus-within:border-purple-500 transition-all bg-[#0f0f14]/50">
                                <span class="bg-white/5 px-3.5 flex items-center text-[#7070a0] text-xs font-semibold select-none border-r border-white/10">
                                    instagram.com/
                                </span>
                                <input type="text" name="instagram" value="{{ old('instagram', $business->instagram) }}" class="flex-1 bg-transparent border-0 outline-none px-3.5 py-2.5 text-white placeholder-white/20 text-xs focus:ring-0 focus:outline-none" placeholder="kullanici_adi">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Salon Açıklaması</label>
                            <textarea name="description" rows="4" class="form-input" placeholder="Salonunuz hakkında kısa bir tanıtım yazısı...">{{ old('description', $business->description) }}</textarea>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-white">Adres ve Konum</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Şehir *</label>
                                <input type="text" name="city" value="{{ old('city', $business->city) }}" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">İlçe *</label>
                                <input type="text" name="district" value="{{ old('district', $business->district) }}" class="form-input" required>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Açık Adres *</label>
                            <input type="text" name="address" value="{{ old('address', $business->address) }}" class="form-input" required>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Enlem (Latitude)</label>
                                <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $business->latitude) }}" class="form-input text-xs" readonly placeholder="Haritadan seçiniz">
                            </div>
                            <div>
                                <label class="form-label">Boylam (Longitude)</label>
                                <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $business->longitude) }}" class="form-input text-xs" readonly placeholder="Haritadan seçiniz">
                            </div>
                        </div>

                        <div>
                            <label class="form-label text-[10px] text-[#7070a0]">Harita Üzerinden Konum Seçin (İğneyi sürükleyebilir veya tıklayabilirsiniz)</label>
                            <div id="map" class="h-60 rounded-xl border border-white/5 mt-1.5 relative z-10"></div>
                        </div>
                    </div>
                </div>

                {{-- Sağ Kolon: Resimler ve Finans --}}
                <div class="space-y-4">
                    {{-- Logo & Cover --}}
                    <div class="glass-card rounded-2xl p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-white">Logo & Kapak Görseli</h3>
                        
                        <div>
                            <label class="form-label">Salon Logosu (Square)</label>
                            @if($business->logo)
                            <div class="mb-3">
                                <img src="{{ asset($business->logo) }}" alt="Logo" class="w-16 h-16 rounded-xl object-cover border border-white/10">
                            </div>
                            @endif
                            <input type="file" name="logo" class="form-input text-xs">
                        </div>

                        <div>
                            <label class="form-label">Kapak Görseli (Hero)</label>
                            @if($business->cover_image)
                            <div class="mb-3">
                                <img src="{{ asset($business->cover_image) }}" alt="Cover" class="w-full h-24 rounded-xl object-cover border border-white/10">
                            </div>
                            @endif
                            <input type="file" name="cover_image" class="form-input text-xs">
                        </div>
                    </div>

                    {{-- Güvence & Kapora Ayarları --}}
                    <div class="glass-card rounded-2xl p-6 space-y-4" x-data="{ deposit: {{ $business->deposit_required ? 'true' : 'false' }} }">
                        <h3 class="text-sm font-semibold text-white">Güvence & Rezervasyon Ayarları</h3>
                        
                        <label class="flex items-center gap-2 p-2 rounded-xl hover:bg-white/5 cursor-pointer transition-colors">
                            <input type="checkbox" name="deposit_required" x-model="deposit" value="1" class="accent-purple-500 w-4 h-4" {{ $business->deposit_required ? 'checked' : '' }}>
                            <div>
                                <p class="text-xs font-semibold text-white">Kapora Zorunlu</p>
                                <p class="text-[10px] text-[#7070a0]">Randevu alırken ön ödeme istenir</p>
                            </div>
                        </label>

                        <div x-show="deposit" x-transition>
                            <label class="form-label">Kapora Oranı (%) *</label>
                            <input type="number" name="deposit_percentage" value="{{ old('deposit_percentage', $business->deposit_percentage ?? 20) }}" min="1" max="100" class="form-input">
                        </div>

                        <div>
                            <label class="form-label">İptal Koşulu (Minimum Saat) *</label>
                            <input type="number" name="cancellation_hours" value="{{ old('cancellation_hours', $business->cancellation_hours) }}" min="0" class="form-input">
                            <span class="text-[10px] text-[#7070a0] mt-1 block">Müşteri randevuya bu süreden az kaldığında iptal edemez.</span>
                        </div>
                    </div>

                    {{-- Personel Prim & Hak Ediş Ayarları --}}
                    <div class="glass-card rounded-2xl p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                            <span>💰</span> Personel Prim & Hak Ediş Sistemi
                        </h3>
                        
                        <label class="flex items-center gap-3 p-3 rounded-xl bg-purple-500/10 border border-purple-500/20 cursor-pointer transition-colors">
                            <input type="checkbox" name="enable_commission" value="1" class="accent-purple-500 w-5 h-5" {{ $business->enable_commission ? 'checked' : '' }}>
                            <div>
                                <p class="text-xs font-bold text-white">Personel Prim Sistemini Etkinleştir</p>
                                <p class="text-[10px] text-[#a0a0c0]">Personellere randevulardan ciro bazlı prim (hak ediş) hesaplamasını ve raporlarını aktif eder.</p>
                            </div>
                        </label>
                    </div>

                    {{-- Kaydet Butonu --}}
                    <button type="submit" class="btn btn-primary w-full py-3 font-semibold shadow-lg shadow-purple-500/20 text-xs">
                        Ayarları Kaydet 🎉
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ── 2. ÇALIŞMA SAATLERİ TAB ── --}}
    <div x-show="activeTab === 'hours'" x-transition.opacity style="display: none;">
        <form method="POST" action="{{ route('admin.settings.hours.update') }}" class="glass-card rounded-2xl p-6 space-y-6">
            @csrf
            @method('PUT')

            <h3 class="text-sm font-semibold text-white mb-2">Haftalık Çalışma Gün ve Saatleri</h3>

            <div class="divide-y divide-white/5">
                @php
                    $days = [
                        0 => 'Pazar',
                        1 => 'Pazartesi',
                        2 => 'Salı',
                        3 => 'Çarşamba',
                        4 => 'Perşembe',
                        5 => 'Cuma',
                        6 => 'Cumartesi'
                    ];
                @endphp

                @foreach($days as $index => $name)
                    @php
                        $hour = $business->hours->firstWhere('day_of_week', $index);
                        $isOpen = $hour ? $hour->is_open : ($index !== 0); // Sunday closed by default
                        $openTime = $hour ? substr($hour->open_time, 0, 5) : '09:00';
                        $closeTime = $hour ? substr($hour->close_time, 0, 5) : '19:00';
                    @endphp
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4" x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-white w-24">{{ $name }}</span>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="hours[{{ $index }}][is_open]" x-model="open" value="1" class="accent-purple-500 w-4 h-4" {{ $isOpen ? 'checked' : '' }}>
                                <span :class="open ? 'text-green-400 font-semibold' : 'text-red-400 font-semibold'" class="text-xs">
                                    <span x-text="open ? 'Açık' : 'Kapalı'"></span>
                                </span>
                            </label>
                        </div>

                        <div class="flex items-center gap-2" x-show="open" x-transition>
                            <input type="time" name="hours[{{ $index }}][open_time]" value="{{ $openTime }}" class="form-input py-1 text-xs w-28">
                            <span class="text-xs text-[#7070a0]">—</span>
                            <input type="time" name="hours[{{ $index }}][close_time]" value="{{ $closeTime }}" class="form-input py-1 text-xs w-28">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="btn btn-primary py-2.5 px-6 font-semibold text-xs">
                    Saatleri Kaydet 📆
                </button>
            </div>
        </form>
    </div>

    {{-- ── 3. GÖRSEL GALERİSİ TAB ── --}}
    <div x-show="activeTab === 'gallery'" x-transition.opacity style="display: none;" class="space-y-6">
        
        {{-- Resim Ekleme Paneli --}}
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Galeriye Yeni Görsel Ekle</h3>
            <form method="POST" action="{{ route('admin.settings.images.store') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-end gap-4">
                @csrf
                <div class="flex-1 w-full">
                    <label class="form-label">Görsel Dosyaları * (Çoklu seçebilirsiniz)</label>
                    <input type="file" name="images[]" class="form-input text-xs" multiple required>
                </div>
                <div class="flex-1 w-full">
                    <label class="form-label">Başlık / Açıklama</label>
                    <input type="text" name="caption" class="form-input text-xs" placeholder="Örn: Salon İçi Görünüm">
                </div>
                <button type="submit" class="btn btn-primary whitespace-nowrap text-xs font-semibold py-2.5 px-5">
                    Görselleri Yükle 🚀
                </button>
            </form>
        </div>

        {{-- Galeri Grid Yapısı --}}
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Aktif Galeri Resimleri ({{ $business->images->count() }})</h3>

            @if($business->images->isEmpty())
            <div class="text-center py-12 text-[#7070a0] text-sm">
                Henüz galeride görsel bulunmuyor. Yukarıdaki formdan yükleyebilirsiniz.
            </div>
            @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($business->images as $img)
                <div class="group relative rounded-xl overflow-hidden border border-white/5 bg-[#121218]">
                    <img src="{{ asset($img->image_path) }}" alt="Gallery Image" class="w-full h-32 object-cover transition-transform duration-300 group-hover:scale-105">
                    
                    @if($img->caption)
                    <div class="p-2 border-t border-white/5">
                        <p class="text-[10px] text-white font-medium truncate">{{ $img->caption }}</p>
                    </div>
                    @endif

                    {{-- Silme Hover Overlay --}}
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <form method="POST" action="{{ route('admin.settings.images.destroy', $img) }}" onsubmit="return confirm('Bu görseli silmek istediğinize emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white rounded-xl p-2.5 shadow-lg">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container {
        font-family: inherit;
    }
    .leaflet-control-zoom {
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        background: #16161f !important;
    }
    .leaflet-control-zoom-in, .leaflet-control-zoom-out {
        background: #16161f !important;
        color: #f1f1f8 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
    }
    .leaflet-control-zoom-in:hover, .leaflet-control-zoom-out:hover {
        background: rgba(255, 255, 255, 0.05) !important;
        color: #e879f9 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Default / Fallback: Kadıköy Center
        let initialLat = {{ $business->latitude ?? 40.9901 }};
        let initialLng = {{ $business->longitude ?? 29.0204 }};

        // Initialize map with CartoDB Dark Matter
        let map = L.map('map').setView([initialLat, initialLng], 14);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
            maxZoom: 20
        }).addTo(map);

        // Draggable Marker
        let marker = L.marker([initialLat, initialLng], {
            draggable: true
        }).addTo(map);

        function updateInputs(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
        }

        // Initialize coordinates inputs
        if (!document.getElementById('latitude').value || !document.getElementById('longitude').value) {
            updateInputs(initialLat, initialLng);
        }

        // Drag marker event
        marker.on('dragend', function (e) {
            let pos = marker.getLatLng();
            updateInputs(pos.lat, pos.lng);
        });

        // Click map event
        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });

        // Handle size redraw on tab toggle to prevent rendering glitches
        let tabBtn = document.querySelector('button[\\@click="activeTab = \'general\'"]');
        if (tabBtn) {
            tabBtn.addEventListener('click', function() {
                setTimeout(() => {
                    map.invalidateSize();
                }, 200);
            });
        }
    });
</script>
@endpush
