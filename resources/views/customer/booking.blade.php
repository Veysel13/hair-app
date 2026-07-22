@extends('layouts.customer')
@section('title', $business->name . ' — Rezervasyon')

@section('content')

{{-- Safe Alpine component registration function to avoid race conditions with Vite module execution --}}
<script>
function initBookingWizard() {
    Alpine.data('bookingWizard', () => ({
        step: 1,
        selectedServices: [],
        selectedStaff: null,
        selectedDate: '',
        selectedTime: '',
        availableSlots: [],
        notes: '',
        name: '',
        phone: '',
        loadingSlots: false,
        services: @json($services),
        staffList: @json($staff),

        init() {
            const urlParams = new URLSearchParams(window.location.search);
            const preselectStaff = urlParams.get('staff_id');
            
            const preselectServices = [];
            for (const [key, value] of urlParams.entries()) {
                if (key.startsWith('service_ids')) {
                    preselectServices.push(value);
                }
            }
            
            if (preselectServices.length > 0) {
                preselectServices.forEach(id => {
                    const parsedId = parseInt(id, 10);
                    const serviceObj = this.services.find(s => s.id === parsedId);
                    if (serviceObj) {
                        this.selectedServices.push(serviceObj);
                    }
                });
            }
            
            if (preselectStaff) {
                this.selectedStaff = parseInt(preselectStaff, 10);
            }
            
            if (this.selectedServices.length > 0 && this.selectedStaff) {
                this.step = 3;
            }
        },
        
        toggleService(service) {
            const idx = this.selectedServices.findIndex(s => s.id === service.id);
            if (idx === -1) {
                this.selectedServices.push(service);
            } else {
                this.selectedServices.splice(idx, 1);
            }
            this.selectedTime = '';
            this.availableSlots = [];
        },
        
        isServiceSelected(id) {
            return this.selectedServices.some(s => s.id === id);
        },
        
        get totalDuration() {
            return this.selectedServices.reduce((sum, s) => sum + s.duration_minutes, 0);
        },
        
        get totalPrice() {
            return this.selectedServices.reduce((sum, s) => sum + s.price, 0);
        },
        
        selectStaff(staffId) {
            this.selectedStaff = staffId;
            this.selectedTime = '';
            this.availableSlots = [];
            if (this.selectedDate) {
                this.fetchSlots();
            }
        },
        
        selectDate(date) {
            this.selectedDate = date;
            this.selectedTime = '';
            this.availableSlots = [];
            if (this.selectedStaff) {
                this.fetchSlots();
            }
        },
        
        async fetchSlots() {
            if (!this.selectedDate || !this.selectedStaff || this.selectedServices.length === 0) return;
            
            this.loadingSlots = true;
            this.availableSlots = [];
            
            try {
                const response = await fetch('{{ route('book.slots', $business) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        date: this.selectedDate,
                        staff_id: this.selectedStaff,
                        service_ids: this.selectedServices.map(s => s.id)
                    })
                });
                
                if (response.ok) {
                    this.availableSlots = await response.json();
                } else {
                    console.error('Slotlar alınırken hata oluştu.');
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loadingSlots = false;
            }
        },
        
        nextStep() {
            if (this.step === 1 && this.selectedServices.length === 0) return;
            if (this.step === 2 && !this.selectedStaff) return;
            if (this.step === 3 && (!this.selectedDate || !this.selectedTime)) return;
            
            this.step++;
        },
        
        prevStep() {
            this.step--;
        }
    }));
}

if (window.Alpine) {
    initBookingWizard();
} else {
    document.addEventListener('alpine:init', initBookingWizard);
}
</script>

<div x-data="bookingWizard">

    {{-- Başlık --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white/3 p-4 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-lg font-extrabold text-white">{{ $business->name }}</h2>
            <p class="text-xs text-[#a0a0c0] mt-0.5">{{ $business->district }}, {{ $business->city }}</p>
        </div>
        @if($business->instagram || $business->website)
        <div class="flex flex-wrap gap-2 text-xs">
            @if($business->instagram)
            <a href="https://instagram.com/{{ $business->instagram }}" target="_blank" class="badge badge-purple flex items-center gap-1 font-bold transition-all hover:scale-105">
                📸 <span>{{ '@' . $business->instagram }}</span>
            </a>
            @endif
            @if($business->website)
            <a href="{{ $business->website }}" target="_blank" class="badge badge-gray flex items-center gap-1 font-bold transition-all hover:scale-105">
                🌐 <span>Web Sitesi</span>
            </a>
            @endif
        </div>
        @endif
    </div>

    {{-- Adım Göstergesi --}}
    <div class="flex items-center gap-1 mb-6">
        <div class="h-1.5 flex-1 rounded-full transition-all duration-300" :class="step >= 1 ? 'gradient-brand' : 'bg-white/5'"></div>
        <div class="h-1.5 flex-1 rounded-full transition-all duration-300" :class="step >= 2 ? 'gradient-brand' : 'bg-white/5'"></div>
        <div class="h-1.5 flex-1 rounded-full transition-all duration-300" :class="step >= 3 ? 'gradient-brand' : 'bg-white/5'"></div>
        <div class="h-1.5 flex-1 rounded-full transition-all duration-300" :class="step >= 4 ? 'gradient-brand' : 'bg-white/5'"></div>
    </div>

    <form method="POST" action="{{ route('book.store', $business) }}">
        @csrf

        {{-- Adım Adım Form Verileri --}}
        <template x-for="s in selectedServices">
            <input type="hidden" name="service_ids[]" :value="s.id">
        </template>
        <input type="hidden" name="staff_id" :value="selectedStaff">
        <input type="hidden" name="appointment_date" :value="selectedDate">
        <input type="hidden" name="start_time" :value="selectedTime">

        {{-- ── STEP 1: HİZMET SEÇİMİ ────────────────── --}}
        <div x-show="step === 1" class="space-y-4">
            <h3 class="text-sm font-semibold text-white">Hizmet Seçimi (En az 1 adet seçin)</h3>
            
            @php $grouped = $services->groupBy(fn($s) => $s->category?->name ?? 'Genel') @endphp
            <div class="space-y-4">
                @foreach($grouped as $catName => $catServices)
                <div>
                    <h4 class="text-xs font-bold text-[#7070a0] uppercase tracking-wider mb-2">{{ $catName }}</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($catServices as $s)
                        <button type="button"
                            @click="toggleService({{ json_encode($s) }})"
                            :class="isServiceSelected({{ $s->id }}) ? 'border-[#d946ef] bg-purple-500/10' : 'border-white/10 hover:border-white/20'"
                            class="flex items-center justify-between p-3.5 rounded-2xl border text-left transition-all">
                            <div>
                                <p class="text-sm font-medium text-white">{{ $s->name }}</p>
                                <p class="text-[11px] text-[#7070a0] mt-0.5">{{ $s->duration_minutes }} dk</p>
                            </div>
                             <div class="text-right">
                                 @if(isset($s->original_price) && $s->original_price > $s->price)
                                 <p class="text-[10px] text-[#7070a0] line-through">{{ number_format($s->original_price, 0, ',', '.') }} ₺</p>
                                 <p class="text-sm font-semibold text-green-400">{{ number_format($s->price, 0, ',', '.') }} ₺</p>
                                 <span class="text-[8px] bg-green-500/10 text-green-400 font-bold px-1.5 py-0.5 rounded-md mt-0.5 inline-block">{{ $s->discount_label }}</span>
                                 @else
                                 <p class="text-sm font-semibold text-[#e879f9]">{{ number_format($s->price, 0, ',', '.') }} ₺</p>
                                 @endif
                                 <div :class="isServiceSelected({{ $s->id }}) ? 'opacity-100' : 'opacity-0'"
                                      class="w-4 h-4 rounded-full gradient-brand flex items-center justify-center ml-auto mt-1 transition-opacity">
                                     <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                 </div>
                             </div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── STEP 2: PERSONEL SEÇİMİ ──────────────── --}}
        <div x-show="step === 2" class="space-y-4">
            <h3 class="text-sm font-semibold text-white">Uzman Personel Seçimi</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <template x-for="s in staffList" :key="s.id">
                    <button type="button"
                        @click="selectStaff(s.id)"
                        :class="selectedStaff === s.id ? 'border-[#d946ef] bg-purple-500/10' : 'border-white/10 hover:border-white/20'"
                        class="flex flex-col items-center p-4 rounded-2xl border text-center transition-all">
                        <div class="w-12 h-12 rounded-xl gradient-brand flex items-center justify-center text-white text-lg font-bold mb-2">
                            <span x-text="s.name.substring(0,1)"></span>
                        </div>
                        <p class="text-xs font-semibold text-white" x-text="s.name"></p>
                        <p class="text-[10px] text-[#7070a0] mt-0.5" x-text="s.title || 'Personel'"></p>
                    </button>
                </template>
            </div>
        </div>

        {{-- ── STEP 3: TARİH & SAAT SEÇİMİ ────────────── --}}
        <div x-show="step === 3" class="space-y-4">
            <h3 class="text-sm font-semibold text-white">Tarih & Saat Seçimi</h3>
            
            <div>
                <label class="form-label text-[10px]">Tarih Seçin</label>
                <input type="date" min="{{ now()->format('Y-m-d') }}"
                    :value="selectedDate"
                    @change="selectDate($event.target.value)"
                    class="form-input text-xs">
            </div>

            <div class="space-y-2">
                <label class="form-label text-[10px]">Müsait Saat Dilimi</label>
                
                <div x-show="loadingSlots" class="text-center py-6 text-sm text-[#7070a0]">
                    Saat dilimleri taranıyor...
                </div>

                <div x-show="!loadingSlots && availableSlots.length === 0 && selectedDate" class="text-center py-6 text-xs text-orange-400">
                    Seçilen tarihte müsait çalışma saati bulunamadı. Lütfen başka bir gün deneyin.
                </div>

                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-12 gap-2">
                    <template x-for="slot in availableSlots" :key="slot">
                        <button type="button"
                            @click="selectedTime = slot"
                            :class="selectedTime === slot ? 'gradient-brand text-white border-transparent' : 'border-white/10 hover:border-white/20 text-[#a0a0c0]'"
                            class="p-2 rounded-xl border text-xs text-center transition-all font-semibold"
                            x-text="slot">
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- ── STEP 4: ÖZET & ONAY ──────────────────── --}}
        <div x-show="step === 4" class="space-y-4">
            <h3 class="text-sm font-semibold text-white">Rezervasyon Özeti</h3>
            
            <div class="glass-card rounded-2xl p-5 space-y-3">
                <div class="flex justify-between border-b border-white/5 pb-2">
                    <span class="text-xs text-[#a0a0c0]">Uzman</span>
                    <span class="text-xs font-semibold text-white" x-text="staffList.find(s => s.id === selectedStaff)?.name"></span>
                </div>
                <div class="flex justify-between border-b border-white/5 pb-2">
                    <span class="text-xs text-[#a0a0c0]">Tarih & Saat</span>
                    <span class="text-xs font-semibold text-white" x-text="selectedDate + ' - ' + selectedTime"></span>
                </div>
                <div class="flex justify-between border-b border-white/5 pb-2">
                    <span class="text-xs text-[#a0a0c0]">Hizmetler</span>
                    <div class="text-right max-w-[200px]">
                        <template x-for="s in selectedServices" :key="s.id">
                            <p class="text-xs font-medium text-white truncate" x-text="s.name"></p>
                        </template>
                    </div>
                </div>
                <div class="flex justify-between border-b border-white/5 pb-2">
                    <span class="text-xs text-[#a0a0c0]">Toplam Süre</span>
                    <span class="text-xs font-semibold text-white" x-text="totalDuration + ' dk'"></span>
                </div>
                <div class="flex justify-between pt-1">
                    <span class="text-xs text-[#a0a0c0] font-bold">Toplam Tutar</span>
                    <span class="text-sm font-bold gradient-text" x-text="totalPrice.toLocaleString('tr-TR') + ' ₺'"></span>
                </div>
            </div>

            {{-- Giriş Yapmamış Ziyaretçiler İçin Bilgiler --}}
            @guest
            <div class="glass-card rounded-2xl p-5 space-y-3">
                <h4 class="text-xs font-bold text-white mb-2">İletişim Bilgileri</h4>
                <div>
                    <label class="form-label text-[10px]">Ad Soyad *</label>
                    <input type="text" name="name" class="form-input text-xs" required placeholder="Adınız ve soyadınız...">
                </div>
                <div>
                    <label class="form-label text-[10px]">Telefon Numarası *</label>
                    <input type="tel" name="phone" class="form-input text-xs" required placeholder="Örn: 05321234567">
                </div>
                <p class="text-[9px] text-[#7070a0]">Telefon numaranız ile sisteme otomatik üyelik tanımlanacaktır.</p>
            </div>
            @endguest

            <div>
                <label class="form-label text-[10px]">Ek Not (Opsiyonel)</label>
                <textarea name="notes" rows="2" class="form-input text-xs" placeholder="İşletmeye veya personele not bırakın..."></textarea>
            </div>
        </div>

        {{-- Navigasyon Butonları --}}
        <div class="flex gap-3 mt-6">
            <button type="button" @click="prevStep()" x-show="step > 1" class="btn btn-secondary flex-1 py-3 text-sm">
                Geri
            </button>
            
            <button type="button" @click="nextStep()" x-show="step < 4"
                :disabled="(step === 1 && selectedServices.length === 0) || (step === 2 && !selectedStaff) || (step === 3 && (!selectedDate || !selectedTime))"
                class="btn btn-primary flex-1 py-3 text-sm">
                Devam Et
            </button>
            
            <button type="submit" x-show="step === 4" class="btn btn-primary flex-1 py-3 text-sm">
                Randevuyu Onayla 🚀
            </button>
        </div>

    </form>
</div>
@endsection
