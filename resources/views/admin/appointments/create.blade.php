@extends('layouts.admin')
@section('title','Yeni Randevu')
@section('page-title','Yeni Randevu Oluştur')
@section('page-subtitle','Müşteri ve hizmet seçerek randevu oluşturun')

@section('content')
<div class="max-w-3xl" 
     data-services="{{ json_encode($services) }}"
     x-data="{
         selectedServices: [],
         totalDuration: 0,
         totalPrice: 0,
         services: [],
         init() {
             try {
                 this.services = JSON.parse(this.$el.getAttribute('data-services') || '[]');
             } catch(e) {
                 console.error('Failed to parse services data', e);
             }
         },
         toggleService(id, duration, price) {
             const idx = this.selectedServices.indexOf(id);
             if (idx === -1) {
                 this.selectedServices.push(id);
                 this.totalDuration += duration;
                 this.totalPrice += price;
             } else {
                 this.selectedServices.splice(idx, 1);
                 this.totalDuration -= duration;
                 this.totalPrice -= price;
             }
         },
         isSelected(id) { return this.selectedServices.includes(id); },
         formatDuration(mins) {
             const h = Math.floor(mins/60), m = mins%60;
             if (h && m) return h+' sa '+m+' dk';
             if (h) return h+' saat';
             return m+' dk';
         }
     }">
    <form method="POST" action="{{ route('admin.appointments.store') }}">
        @csrf
        <div class="space-y-4">

            {{-- Müşteri + Personel --}}
            <div class="glass-card rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-white mb-4">Müşteri & Personel</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Müşteri *</label>
                        <select name="customer_id" class="form-input" required>
                            <option value="">Müşteri seçin...</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id')==$c->id ? 'selected':'' }}>{{ $c->name }} ({{ $c->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Personel *</label>
                        <select name="staff_id" class="form-input" required>
                            <option value="">Personel seçin...</option>
                            @foreach($staff as $s)
                            <option value="{{ $s->id }}" {{ old('staff_id')==$s->id ? 'selected':'' }}>{{ $s->name }} — {{ $s->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Tarih *</label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date', now()->addDay()->format('Y-m-d')) }}"
                            min="{{ now()->format('Y-m-d') }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Başlangıç Saati *</label>
                        <input type="time" name="start_time" value="{{ old('start_time','09:00') }}" class="form-input" required>
                    </div>
                </div>
            </div>

            {{-- Hizmet Seçimi --}}
            <div class="glass-card rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-white mb-4">Hizmetler *</h2>
                <template x-for="id in selectedServices">
                    <input type="hidden" name="service_ids[]" :value="id">
                </template>

                @php $grouped = $services->groupBy(fn($s) => $s->category?->name ?? 'Genel') @endphp
                <div class="space-y-4">
                    @foreach($grouped as $catName => $catServices)
                    <div>
                        <p class="text-xs font-semibold text-[#7070a0] uppercase tracking-wider mb-2">{{ $catName }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($catServices as $s)
                            <button type="button"
                                @click="toggleService({{ $s->id }}, {{ $s->duration_minutes }}, {{ $s->price }})"
                                :class="isSelected({{ $s->id }}) ? 'border-[#d946ef] bg-purple-500/10' : 'border-white/10 hover:border-white/20'"
                                class="flex items-center justify-between p-3 rounded-xl border text-left transition-all">
                                <div>
                                    <p class="text-sm font-medium text-white">{{ $s->name }}</p>
                                    <p class="text-xs text-[#7070a0]">{{ $s->duration_minutes }} dk</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-[#e879f9]">{{ number_format($s->price,0,',','.') }} ₺</p>
                                    <div :class="isSelected({{ $s->id }}) ? 'opacity-100':'opacity-0'"
                                         class="w-4 h-4 rounded-full gradient-brand flex items-center justify-center ml-auto mt-0.5 transition-opacity">
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

            {{-- Notlar + Kaynak --}}
            <div class="glass-card rounded-2xl p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Not (Opsiyonel)</label>
                        <textarea name="notes" rows="3" class="form-input" placeholder="Müşteri notu...">{{ old('notes') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Kaynak</label>
                        <select name="source" class="form-input">
                            <option value="phone">Telefon</option>
                            <option value="walk_in">Yüz Yüze</option>
                            <option value="app">Uygulama</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Özet + Gönder --}}
            <div class="glass-card rounded-2xl p-6 border border-purple-500/20">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex gap-6">
                        <div>
                            <p class="text-xs text-[#7070a0]">Toplam Süre</p>
                            <p class="text-lg font-bold text-white" x-text="formatDuration(totalDuration)">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-[#7070a0]">Toplam Tutar</p>
                            <p class="text-lg font-bold gradient-text" x-text="totalPrice.toLocaleString('tr-TR') + ' ₺'">—</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">İptal</a>
                        <button type="submit" class="btn btn-primary" :disabled="selectedServices.length === 0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Randevu Oluştur
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
