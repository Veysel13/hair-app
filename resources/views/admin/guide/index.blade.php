@extends('layouts.admin')
@section('title', 'Kullanım Kılavuzu & İşletme Rehberi')
@section('page-title', 'Kullanım Kılavuzu & Yardım Merkezi')
@section('page-subtitle', 'HairApp platformunun tüm modüllerini seslendirme ve video rehberler ile keşfedin.')

@push('head')
<script>
function speakGuide(text) {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'tr-TR';
        utterance.rate = 0.95;
        utterance.pitch = 1.0;
        window.speechSynthesis.speak(utterance);
    } else {
        alert('Tarayıcınız sesli Türkçe anlatımı desteklemiyor.');
    }
}

function stopGuide() {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
    }
}
</script>
@endpush

@section('content')
<div x-data="{ activeTab: 'start' }" class="space-y-6">

    {{-- Üst Hoşgeldiniz ve Modül Gezinme Barı --}}
    <div class="glass-card rounded-2xl p-6 bg-gradient-to-r from-purple-900/30 via-[#16161f] to-[#12121a] border border-purple-500/20">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <span>📚</span> Salonim İşletme Kullanım Kılavuzu
                </h2>
                <p class="text-xs text-[#a0a0c0] mt-1">
                    Salonunuzu dijitalleştirmek, müşteri memnuniyetini ve cironuzu artırmak için hazırlanan Türkçe seslendirmeli ve videolu interaktif rehber.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('business.showcase', $business->slug) }}" target="_blank" class="btn btn-secondary text-xs font-bold flex items-center gap-1.5 hover:border-purple-500/40">
                    <span>👁️</span> Canlı Sayfanı Önizle ↗
                </a>
            </div>
        </div>

        {{-- Sekme Butonları --}}
        <div class="flex gap-2 mt-6 overflow-x-auto pb-2 border-b border-white/10 no-scrollbar">
            <button @click="activeTab = 'start'" :class="activeTab === 'start' ? 'btn-primary' : 'btn-secondary text-[#a0a0c0]'" class="btn btn-sm text-xs font-semibold whitespace-nowrap">
                🚀 Hızlı Başlangıç & Kurulum
            </button>
            <button @click="activeTab = 'appointments'" :class="activeTab === 'appointments' ? 'btn-primary' : 'btn-secondary text-[#a0a0c0]'" class="btn btn-sm text-xs font-semibold whitespace-nowrap">
                📅 Randevular & Takvim
            </button>
            <button @click="activeTab = 'customers'" :class="activeTab === 'customers' ? 'btn-primary' : 'btn-secondary text-[#a0a0c0]'" class="btn btn-sm text-xs font-semibold whitespace-nowrap">
                👤 Müşteri CRM & VIP Notlar
            </button>
            <button @click="activeTab = 'formulas'" :class="activeTab === 'formulas' ? 'btn-primary' : 'btn-secondary text-[#a0a0c0]'" class="btn btn-sm text-xs font-semibold whitespace-nowrap">
                📝 İşlem Detayı & Formüller
            </button>
            <button @click="activeTab = 'staff'" :class="activeTab === 'staff' ? 'btn-primary' : 'btn-secondary text-[#a0a0c0]'" class="btn btn-sm text-xs font-semibold whitespace-nowrap">
                💰 Personel & Prim (Hak Ediş)
            </button>
            <button @click="activeTab = 'services'" :class="activeTab === 'services' ? 'btn-primary' : 'btn-secondary text-[#a0a0c0]'" class="btn btn-sm text-xs font-semibold whitespace-nowrap">
                ✂️ Hizmetler & Fiyatlar
            </button>
            <button @click="activeTab = 'campaigns'" :class="activeTab === 'campaigns' ? 'btn-primary' : 'btn-secondary text-[#a0a0c0]'" class="btn btn-sm text-xs font-semibold whitespace-nowrap">
                🔥 Kampanyalar & İndirimler
            </button>
            <button @click="activeTab = 'finance'" :class="activeTab === 'finance' ? 'btn-primary' : 'btn-secondary text-[#a0a0c0]'" class="btn btn-sm text-xs font-semibold whitespace-nowrap">
                💳 Finans, Stok & Yorumlar
            </button>
        </div>
    </div>

    {{-- ── TAB 1: 🚀 HIZLI BAŞLANGIÇ & SALON KURULUMU ── --}}
    <div x-show="activeTab === 'start'" x-transition.opacity class="space-y-6">
        
        {{-- Sesli Anlatım Oyuncusu --}}
        <div x-data="{ isSpeaking: false }" class="bg-gradient-to-r from-purple-950/40 via-indigo-950/20 to-[#12121a] p-4 rounded-xl border border-purple-500/30 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 text-lg flex-shrink-0 animate-pulse">
                    🎙️
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white">Sesli Sayfa Rehberi ve Anlatımı</h4>
                    <p class="text-[11px] text-[#a0a0c0]">Hızlı Başlangıç ve Salon Kurulum adımlarını Türkçe sesli dinleyin.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="if(isSpeaking){ stopGuide(); isSpeaking=false; } else { speakGuide('Hızlı başlangıç rehberine hoş geldiniz. Salon ayarları sayfasından logonuzu, kapak fotoğrafınızı, adresinizi ve Instagram adresinizi girerek müşterilerinizin online randevu almasını sağlayabilirsiniz.'); isSpeaking=true; }" class="btn btn-primary btn-sm text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-purple-500/20">
                    <span x-text="isSpeaking ? '⏸️ Anlatımı Durdur' : '🔊 Sesli Anlatımı Başlat'"></span>
                </button>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                <span>🚀</span> 1. Adım: Salon Profilinizi Yapılandırın
            </h3>
            
            <p class="text-xs text-[#d0d0e0] leading-relaxed">
                Müşterilerinizin salonunuzu tanıması ve online randevu alabilmesi için <strong>Salon Ayarları (`/admin/settings`)</strong> sekmesinden gerekli temel bilgileri eksiksiz doldurmalısınız.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-purple-400">🖼️ Logo & Kapak Görselleri</h4>
                    <p class="text-xs text-[#7070a0]">
                        Salonunuzun kare logosunu ve yatay kapak (Hero) fotoğrafını yükleyin. Çözünürlüğü yüksek ve şık görseller müşterilerin güven duymasını sağlar.
                    </p>
                </div>

                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-purple-400">📍 Harita Konumu & Açık Adres</h4>
                    <p class="text-xs text-[#7070a0]">
                        İl, ilçe ve açık adresinizi girdikten sonra harita üzerindeki iğneyi salonunuzun tam kapı girişine sürükleyin. Müşterileriniz yol tarifi alabilir.
                    </p>
                </div>

                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-purple-400">📸 Instagram Bağlantısı</h4>
                    <p class="text-xs text-[#7070a0]">
                        Instagram kullanıcı adınızı (örn: `lux_hair_salon`) ekleyin. Müşterileriniz tanıtım sayfasından Instagram profilinize tek tıkla ulaşır.
                    </p>
                </div>

                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-purple-400">👁️ Canlı Sayfa Önizleme</h4>
                    <p class="text-xs text-[#7070a0]">
                        Ayarlar sayfasında salon adınızın hemen altındaki <strong>"Sayfayı Önizle"</strong> butonuna basarak müşterilerinizin göreceği web sayfasını yeni sekmede inceleyin.
                    </p>
                </div>
            </div>

            <div class="pt-3 flex justify-end">
                <a href="{{ route('admin.settings.edit') }}" class="btn btn-primary btn-sm text-xs font-bold">
                    Salon Ayarlarına Git ⚙️
                </a>
            </div>
        </div>
    </div>

    {{-- ── TAB 2: 📅 RANDEVULAR & TAKVİM ── --}}
    <div x-show="activeTab === 'appointments'" x-transition.opacity class="space-y-6" style="display: none;">
        
        {{-- Sesli Anlatım Oyuncusu --}}
        <div x-data="{ isSpeaking: false }" class="bg-gradient-to-r from-purple-950/40 via-indigo-950/20 to-[#12121a] p-4 rounded-xl border border-purple-500/30 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 text-lg flex-shrink-0 animate-pulse">
                    🎙️
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white">Sesli Sayfa Rehberi ve Anlatımı</h4>
                    <p class="text-[11px] text-[#a0a0c0]">Randevu yönetimi ve durum güncellemelerini sesli dinleyin.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="if(isSpeaking){ stopGuide(); isSpeaking=false; } else { speakGuide('Randevu ve takvim modülündesiniz. Bekliyor, Onaylı, Başladı ve Tamamlandı durumlarını güncelleyebilir, gelmeme durumlarını engellemek için kapora zorunluluğunu aktifleştirebilirsiniz.'); isSpeaking=true; }" class="btn btn-primary btn-sm text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-purple-500/20">
                    <span x-text="isSpeaking ? '⏸️ Anlatımı Durdur' : '🔊 Sesli Anlatımı Başlat'"></span>
                </button>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                <span>📅</span> 2. Adım: Randevuları Yönetin ve Takip Edin
            </h3>

            <p class="text-xs text-[#d0d0e0] leading-relaxed">
                Müşterilerinizin web sitenizden aldığı online randevular ve telefonla/yüz yüze aldığınız randevular <strong>Randevular (`/admin/appointments`)</strong> ve <strong>Takvim (`/admin/calendar`)</strong> ekranında listelenir.
            </p>

            <div class="space-y-3">
                <div class="bg-white/3 p-4 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-white mb-2">⚡ Randevu Durum Geçişleri</h4>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="badge badge-warning">Bekliyor</span> → Onay bekleyen yeni randevu.
                        <span class="badge badge-info">Onaylı</span> → Salon tarafından kabul edilen randevu.
                        <span class="badge badge-purple">Başladı</span> → Müşteri salonda, koltukta işlemde.
                        <span class="badge badge-success">Tamamlandı</span> → İşlem bitti, ödeme alındı. (Müşteri istatistikleri ve prim otomatik işlenir).
                    </div>
                </div>

                <div class="bg-white/3 p-4 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-white mb-1">🛡️ Kapora & Güvence Sistemi</h4>
                    <p class="text-xs text-[#a0a0c0]">
                        Randevulara gelmeme (No-Show) durumunu engellemek için Salon Ayarlarından <strong>"Kapora Zorunlu"</strong> özelliğini aktif edebilirsiniz. Randevu alırken belirlenen oran (örn: %20) ön ödeme alınır.
                    </p>
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <a href="{{ route('admin.calendar.index') }}" class="btn btn-secondary btn-sm text-xs font-bold">
                    Takvimi Aç 📆
                </a>
                <a href="{{ route('admin.appointments.index') }}" class="btn btn-primary btn-sm text-xs font-bold">
                    Randevu Listesini Gör 📋
                </a>
            </div>
        </div>
    </div>

    {{-- ── TAB 3: 👤 MÜŞTERİ CRM & VIP NOTLAR ── --}}
    <div x-show="activeTab === 'customers'" x-transition.opacity class="space-y-6" style="display: none;">
        
        {{-- Sesli Anlatım Oyuncusu --}}
        <div x-data="{ isSpeaking: false }" class="bg-gradient-to-r from-purple-950/40 via-indigo-950/20 to-[#12121a] p-4 rounded-xl border border-purple-500/30 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 text-lg flex-shrink-0 animate-pulse">
                    🎙️
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white">Sesli Sayfa Rehberi ve Anlatımı</h4>
                    <p class="text-[11px] text-[#a0a0c0]">Müşteri CRM kartları ve VIP alışkanlık notlarını sesli dinleyin.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="if(isSpeaking){ stopGuide(); isSpeaking=false; } else { speakGuide('Müşteri yönetimi modülündesiniz. Müşterilerinizin saç tipi, boya rengi, alerjileri ve sevdiği kahve türü gibi VIP notları kaydederek personellerinizin randevu günü harika bir ağırlama yapmasını sağlayabilirsiniz.'); isSpeaking=true; }" class="btn btn-primary btn-sm text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-purple-500/20">
                    <span x-text="isSpeaking ? '⏸️ Anlatımı Durdur' : '🔊 Sesli Anlatımı Başlat'"></span>
                </button>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                <span>👤</span> 3. Adım: Müşteri Kartları & VIP Not Yönetimi
            </h3>

            <p class="text-xs text-[#d0d0e0] leading-relaxed">
                Salonunuza gelen her müşteri için otomatik bir CRM kartı oluşturulur. <strong>Müşteriler (`/admin/customers`)</strong> sekmesinden tüm müşterilerinizi ve alışkanlıklarını yönetebilirsiniz.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-purple-400">⭐ VIP Rozeti ve Tercih Notları</h4>
                    <p class="text-xs text-[#a0a0c0]">
                        Müşterinizin saç tipi (kıvırcık, düz), saç rengi, alerjileri ve özel tercihleri (örn: <i>"Sütlü filtre kahve sever, sohbet etmeyi sever"</i>) kaydedilir.
                    </p>
                </div>

                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-purple-400">🔗 Randevu Günü Personel Ekranı</h4>
                    <p class="text-xs text-[#a0a0c0]">
                        Randevu detayına giren personel, müşterinin bu VIP notlarını ve geçmişini anında görerek müşteriyi adıyla ve sevdiği ikramla karşılar.
                    </p>
                </div>
            </div>

            <div class="bg-purple-500/10 border border-purple-500/20 p-4 rounded-xl text-xs text-purple-200">
                💡 <strong>İpucu:</strong> Randevu Detay sayfasında sağ üstteki <strong>"👤 Müşteri Kartı ↗"</strong> butonuna tıklayarak müşterinin tüm geçmişini yeni sekmede inceleyebilirsiniz.
            </div>

            <div class="pt-3 flex justify-end">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-primary btn-sm text-xs font-bold">
                    Müşteri Listesini Aç 👥
                </a>
            </div>
        </div>
    </div>

    {{-- ── TAB 4: 📝 İŞLEM DETAYI & FORMÜLLER ── --}}
    <div x-show="activeTab === 'formulas'" x-transition.opacity class="space-y-6" style="display: none;">
        
        {{-- Sesli Anlatım Oyuncusu --}}
        <div x-data="{ isSpeaking: false }" class="bg-gradient-to-r from-purple-950/40 via-indigo-950/20 to-[#12121a] p-4 rounded-xl border border-purple-500/30 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 text-lg flex-shrink-0 animate-pulse">
                    🎙️
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white">Sesli Sayfa Rehberi ve Anlatımı</h4>
                    <p class="text-[11px] text-[#a0a0c0]">Boya formülleri ve işlem teknik detay kaydını sesli dinleyin.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="if(isSpeaking){ stopGuide(); isSpeaking=false; } else { speakGuide('İşlem formülleri modülündesiniz. Randevu sayfasından veya müşteri kartından boya kodlarını, kullanılan bakım ürünlerini ve öncesi sonrası fotoğraflarını kaydedebilirsiniz.'); isSpeaking=true; }" class="btn btn-primary btn-sm text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-purple-500/20">
                    <span x-text="isSpeaking ? '⏸️ Anlatımı Durdur' : '🔊 Sesli Anlatımı Başlat'"></span>
                </button>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                <span>📝</span> 4. Adım: Randevu İşlem & Boya Formül Kaydı
            </h3>

            <p class="text-xs text-[#d0d0e0] leading-relaxed">
                Müşterinize uyguladığınız saç boyası oranlarını, stil formüllerini veya bakım reçetelerini kaydetmek bir sonraki randevuda tam aynı rengi yakalamanızı sağlar.
            </p>

            <div class="space-y-3">
                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-white">🧪 Formül ve Kullanılan Ürünler</h4>
                    <p class="text-xs text-[#7070a0]">
                        Randevu Detay ekranındaki <strong>"📝 Bu Randevunun İşlem & Formül Kaydı"</strong> alanından boya kodlarını (örn: `Koleston 7.1, %6 Oksidan, 30gr`) ve kullanılan ürünleri girebilirsiniz.
                    </p>
                </div>

                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-white">📷 İşlem Öncesi & Sonrası Fotoğraflar</h4>
                    <p class="text-xs text-[#7070a0]">
                        İşlem başlamadan önceki ve bittikten sonraki fotoğrafları yükleyerek müşterinizin dönüşüm galerisini arşivleyebilirsiniz.
                    </p>
                </div>
            </div>

            <div class="bg-green-500/10 border border-green-500/20 p-4 rounded-xl text-xs text-green-200">
                🔄 <strong>Çift Yönlü Eşitleme:</strong> Randevu sayfasından girdiğiniz formül ve fotoğraflar anında müşterinin profil kartındaki işlem geçmişiyle eşzamanlı güncellenir.
            </div>
        </div>
    </div>

    {{-- ── TAB 5: 💰 PERSONEL & PRİM (HAK EDİŞ) ── --}}
    <div x-show="activeTab === 'staff'" x-transition.opacity class="space-y-6" style="display: none;">
        
        {{-- Sesli Anlatım Oyuncusu --}}
        <div x-data="{ isSpeaking: false }" class="bg-gradient-to-r from-purple-950/40 via-indigo-950/20 to-[#12121a] p-4 rounded-xl border border-purple-500/30 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 text-lg flex-shrink-0 animate-pulse">
                    🎙️
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white">Sesli Sayfa Rehberi ve Anlatımı</h4>
                    <p class="text-[11px] text-[#a0a0c0]">Personel yönetimi ve prim hak ediş hesaplamalarını sesli dinleyin.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="if(isSpeaking){ stopGuide(); isSpeaking=false; } else { speakGuide('Personel ve prim sistemindesiniz. Salon ayarlarından prim modülünü aktif ettikten sonra çalışanlarınızın varsayılan prim oranlarını yüzde olarak belirleyebilir ve prim hak ediş raporundan dönemsel prim alacaklarını inceleyebilirsiniz.'); isSpeaking=true; }" class="btn btn-primary btn-sm text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-purple-500/20">
                    <span x-text="isSpeaking ? '⏸️ Anlatımı Durdur' : '🔊 Sesli Anlatımı Başlat'"></span>
                </button>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                <span>💰</span> 5. Adım: Personel Yönetimi & Prim (Hak Ediş) Takibi
            </h3>

            <p class="text-xs text-[#d0d0e0] leading-relaxed">
                Çalışanlarınızı ekleyin, çalışma saatlerini belirleyin ve ciro üzerinden otomatik prim hesaplamasını aktif edin.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-purple-400">⚙️ Prim Modülünü Aktif Edin</h4>
                    <p class="text-xs text-[#a0a0c0]">
                        Salon Ayarlarından <strong>"Personel Prim Sistemini Etkinleştir"</strong> seçeneğini açın.
                    </p>
                </div>

                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-purple-400">📊 Varsayılan Prim Oranı (%)</h4>
                    <p class="text-xs text-[#a0a0c0]">
                        Her personel için varsayılan prim oranını (örn: %30) belirleyin. Randevular tamamlandıkça prim otomatik hesaplanır.
                    </p>
                </div>
            </div>

            <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                <h4 class="text-xs font-bold text-white">🏆 Prim & Hak Ediş Raporu (`/admin/staff/commissions`)</h4>
                <p class="text-xs text-[#7070a0]">
                    Seçtiğiniz tarih aralığına göre tüm çalışanlarınızın salona kazandırdığı ciroyu, tamamladığı randevu adedini ve hak ettiği toplam net prim tutarını liderlik tablosunda görüntüleyin.
                </p>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary btn-sm text-xs font-bold">
                    Personelleri Yönet 👥
                </a>
                @if($business->enable_commission)
                <a href="{{ route('admin.staff.commissions') }}" class="btn btn-primary btn-sm text-xs font-bold">
                    Prim Raporunu Aç 💰
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── TAB 6: ✂️ HİZMETLER & FİYATLAR ── --}}
    <div x-show="activeTab === 'services'" x-transition.opacity class="space-y-6" style="display: none;">
        
        {{-- Sesli Anlatım Oyuncusu --}}
        <div x-data="{ isSpeaking: false }" class="bg-gradient-to-r from-purple-950/40 via-indigo-950/20 to-[#12121a] p-4 rounded-xl border border-purple-500/30 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 text-lg flex-shrink-0 animate-pulse">
                    🎙️
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white">Sesli Sayfa Rehberi ve Anlatımı</h4>
                    <p class="text-[11px] text-[#a0a0c0]">Hizmet kataloğu, süre ve fiyatlandırma adımlarını sesli dinleyin.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="if(isSpeaking){ stopGuide(); isSpeaking=false; } else { speakGuide('Hizmetler modülündesiniz. Salonunuzda sunduğunuz tüm işlemleri kategorilere ayırarak, işlem süresi ve fiyatı ile tanımlayabilirsiniz.'); isSpeaking=true; }" class="btn btn-primary btn-sm text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-purple-500/20">
                    <span x-text="isSpeaking ? '⏸️ Anlatımı Durdur' : '🔊 Sesli Anlatımı Başlat'"></span>
                </button>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                <span>✂️</span> 6. Adım: Hizmet Kataloğu & Fiyatlandırma
            </h3>

            <p class="text-xs text-[#d0d0e0] leading-relaxed">
                Salonunuzda sunduğunuz tüm işlemleri (Saç Kesimi, Boya, Fön, Manikür vb.) <strong>Hizmetler (`/admin/services`)</strong> sekmesinden tanımlayın.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-white/3 p-3 rounded-xl border border-white/5">
                    <p class="text-xs font-bold text-white">⏱️ İşlem Süresi (dk)</p>
                    <p class="text-[11px] text-[#7070a0] mt-1">Takvimde çakışmaları önlemek için hizmet süresini doğru belirleyin.</p>
                </div>
                <div class="bg-white/3 p-3 rounded-xl border border-white/5">
                    <p class="text-xs font-bold text-white">🏷️ Standart Fiyat (₺)</p>
                    <p class="text-[11px] text-[#7070a0] mt-1">Hizmetin liste satış fiyatını tanımlayın.</p>
                </div>
                <div class="bg-white/3 p-3 rounded-xl border border-white/5">
                    <p class="text-xs font-bold text-white">👥 Personel Ataması</p>
                    <p class="text-[11px] text-[#7070a0] mt-1">Hangi çalışanların bu hizmeti verebileceğini seçin.</p>
                </div>
            </div>

            <div class="pt-3 flex justify-end">
                <a href="{{ route('admin.services.index') }}" class="btn btn-primary btn-sm text-xs font-bold">
                    Hizmet Kataloğunu Aç ✂️
                </a>
            </div>
        </div>
    </div>

    {{-- ── TAB 7: 🔥 KAMPANYALAR & İNDİRİMLER ── --}}
    <div x-show="activeTab === 'campaigns'" x-transition.opacity class="space-y-6" style="display: none;">
        
        {{-- Sesli Anlatım Oyuncusu --}}
        <div x-data="{ isSpeaking: false }" class="bg-gradient-to-r from-purple-950/40 via-indigo-950/20 to-[#12121a] p-4 rounded-xl border border-purple-500/30 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 text-lg flex-shrink-0 animate-pulse">
                    🎙️
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white">Sesli Sayfa Rehberi ve Anlatımı</h4>
                    <p class="text-[11px] text-[#a0a0c0]">Kampanyalar ve otomatik indirim motorunu sesli dinleyin.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="if(isSpeaking){ stopGuide(); isSpeaking=false; } else { speakGuide('Kampanyalar modülündesiniz. Doğum günü indirimleri ve özel fırsatlar tanımlayarak müşterilerinizi çekebilir, üstü çizili yeşil indirimli fiyatlar ile online randevu sayınızı yükseltebilirsiniz.'); isSpeaking=true; }" class="btn btn-primary btn-sm text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-purple-500/20">
                    <span x-text="isSpeaking ? '⏸️ Anlatımı Durdur' : '🔊 Sesli Anlatımı Başlat'"></span>
                </button>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                <span>🔥</span> 7. Adım: İndirim Kampanyaları ile Cironuzu Artırın
            </h3>

            <p class="text-xs text-[#d0d0e0] leading-relaxed">
                <strong>Kampanyalar (`/admin/campaigns`)</strong> modülü ile Doğum Günü Fırsatları veya Sezon İndirimleri tanımlayabilirsiniz.
            </p>

            <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                <h4 class="text-xs font-bold text-purple-400">🏷️ Otomatik İndirim Motoru</h4>
                <p class="text-xs text-[#a0a0c0]">
                    Müşteri online randevu alırken sistem aktif kampanyaları tarar, eski fiyatın üstünü gri çizip **yeşil indirimli fiyatı** gösterir. Randevu onaylandığında kampanya kullanım logları otomatik tutulur.
                </p>
            </div>

            <div class="pt-3 flex justify-end">
                <a href="{{ route('admin.campaigns.index') }}" class="btn btn-primary btn-sm text-xs font-bold">
                    Kampanyaları Yönet 🔥
                </a>
            </div>
        </div>
    </div>

    {{-- ── TAB 8: 💳 FİNANS, STOK & YORUMLAR ── --}}
    <div x-show="activeTab === 'finance'" x-transition.opacity class="space-y-6" style="display: none;">
        
        {{-- Sesli Anlatım Oyuncusu --}}
        <div x-data="{ isSpeaking: false }" class="bg-gradient-to-r from-purple-950/40 via-indigo-950/20 to-[#12121a] p-4 rounded-xl border border-purple-500/30 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 text-lg flex-shrink-0 animate-pulse">
                    🎙️
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white">Sesli Sayfa Rehberi ve Anlatımı</h4>
                    <p class="text-[11px] text-[#a0a0c0]">Finans, kasa, stok ve müşteri yorum yönetimini sesli dinleyin.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="if(isSpeaking){ stopGuide(); isSpeaking=false; } else { speakGuide('Finans ve stok modülündesiniz. Günlük nakit kart ödeme dağılımını görebilir, giderlerinizi kaydedip net karınızı izleyebilir, kritik stok uyarıları ile ürün stoklarınızı yönetebilirsiniz.'); isSpeaking=true; }" class="btn btn-primary btn-sm text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-purple-500/20">
                    <span x-text="isSpeaking ? '⏸️ Anlatımı Durdur' : '🔊 Sesli Anlatımı Başlat'"></span>
                </button>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                <span>💳</span> 8. Adım: Kasa, Gider ve Net Kâr Takibi (Financial Dashboard)
            </h3>

            <p class="text-xs text-[#d0d0e0] leading-relaxed">
                Salonunuzun gün sonu nakit/kart kasasını, toplanan bahşişleri, gider defterini ve ay sonu Net Kâr / Zarar grafiğini yönetin.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-purple-400">⚡ Hızlı Gün Sonu & Adisyon</h4>
                    <p class="text-xs text-[#7070a0]">
                        Randevu detayından tek tıkla <strong>Nakit, Kredi Kartı veya Parçalı Ödeme</strong> seçerek adisyonu kapatın. Bahşiş (tip) ve fiş notunu işleyin.
                    </p>
                </div>

                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-purple-400">🏢 Gün Sonu Kasa Özeti</h4>
                    <p class="text-xs text-[#7070a0]">
                        Finans sayfasında bugünkü Nakit Kasa, Kart Kasa, Bahşiş Kasa ve Net Gün Sonu Bakiye kartlarını canlı takip edin.
                    </p>
                </div>

                <div class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-2">
                    <h4 class="text-xs font-bold text-purple-400">🖨️ Gün Sonu Z-Raporu (A4 PDF)</h4>
                    <p class="text-xs text-[#7070a0]">
                        Akşam dükkanı kapatırken tek tıkla <strong>Gün Sonu Z-Raporu</strong> alın. Nakit/Kart kasası, bahşişler, personel prim hak edişleri ve adisyon dökümü A4 formatında basılır.
                    </p>
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <a href="{{ route('admin.support.index') }}" class="btn btn-secondary btn-sm text-xs font-bold">
                    Canlı Destek & İletişim 💬
                </a>
                <a href="{{ route('admin.finance.index') }}" class="btn btn-primary btn-sm text-xs font-bold">
                    Finans & Kasa Paneli 💳
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
