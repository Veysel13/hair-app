@extends('layouts.admin')
@section('title','Müşteri Profili')
@section('page-title', $customerProfile->customer->name)
@section('page-subtitle','Müşteri CRM Kartı')
@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    {{-- Sol: Profil Kartı --}}
    <div class="space-y-4">
        <div class="glass-card rounded-2xl p-6">
            <div class="text-center mb-5">
                <div class="w-16 h-16 rounded-2xl gradient-brand flex items-center justify-center text-white text-2xl font-bold mx-auto mb-3">{{ substr($customerProfile->customer->name,0,1) }}</div>
                <h2 class="text-base font-bold text-white">{{ $customerProfile->customer->name }}</h2>
                <p class="text-sm text-[#7070a0]">{{ $customerProfile->customer->phone }}</p>
                @if($customerProfile->is_vip)<span class="badge badge-purple mt-1">⭐ VIP Müşteri</span>@endif
            </div>
            <div class="grid grid-cols-3 gap-2 mb-5">
                <div class="text-center p-2 rounded-xl bg-white/3">
                    <p class="text-base font-bold text-white">{{ $customerProfile->total_visits }}</p>
                    <p class="text-[10px] text-[#7070a0]">Ziyaret</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white/3">
                    <p class="text-sm font-bold text-[#e879f9]">{{ number_format($customerProfile->total_spent,0,',','.') }}₺</p>
                    <p class="text-[10px] text-[#7070a0]">Harcama</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white/3">
                    <p class="text-base font-bold text-white">{{ $customerProfile->days_since_last_visit ?? '—' }}</p>
                    <p class="text-[10px] text-[#7070a0]">Gün önce</p>
                </div>
            </div>
            {{-- Notlar formu --}}
            <form method="POST" action="{{ route('admin.customers.update',$customerProfile) }}" class="space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="form-label">Saç Tipi</label>
                    <input type="text" name="hair_type" value="{{ $customerProfile->hair_type }}" class="form-input" placeholder="Kıvırcık, düz...">
                </div>
                <div>
                    <label class="form-label">Saç Rengi</label>
                    <input type="text" name="hair_color" value="{{ $customerProfile->hair_color }}" class="form-input" placeholder="Mevcut renk">
                </div>
                <div>
                    <label class="form-label">Alerjiler / Hassasiyetler</label>
                    <textarea name="allergies" rows="2" class="form-input" placeholder="Varsa belirtin...">{{ $customerProfile->allergies }}</textarea>
                </div>
                <div>
                    <label class="form-label">Notlar</label>
                    <textarea name="notes" rows="3" class="form-input">{{ $customerProfile->notes }}</textarea>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_vip" value="1" {{ $customerProfile->is_vip ? 'checked':'' }} class="w-4 h-4 accent-purple-500">
                    <span class="text-sm text-[#a0a0c0]">VIP Müşteri</span>
                </label>
                <button type="submit" class="btn btn-primary w-full">Kaydet</button>
            </form>
        </div>
    </div>

    {{-- Sağ: Randevu Geçmişi --}}
    <div class="xl:col-span-2 space-y-3">
        <h3 class="text-sm font-semibold text-white">Randevu Geçmişi ({{ $appointments->count() }})</h3>
        @forelse($appointments as $appt)
        <div class="glass-card rounded-2xl p-4 appt-{{ $appt->status }}">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <p class="text-sm font-semibold text-white">{{ $appt->appointment_date->format('d M Y') }}</p>
                        <span class="text-xs text-[#7070a0]">{{ substr($appt->start_time,0,5) }}</span>
                        <span class="badge {{ match($appt->status){ 'completed'=>'badge-success','cancelled'=>'badge-danger','pending'=>'badge-warning','confirmed'=>'badge-info', default=>'badge-gray' } }}">
                            {{ match($appt->status){ 'completed'=>'Tamamlandı','cancelled'=>'İptal','pending'=>'Bekliyor','confirmed'=>'Onaylı','no_show'=>'Gelmedi', default=>$appt->status } }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @foreach($appt->services as $s)
                        <span class="badge badge-gray text-xs">{{ $s->service->name }}</span>
                        @endforeach
                    </div>
                    <p class="text-xs text-[#7070a0] mt-1">👤 {{ $appt->staff->name }}</p>
                </div>
                <div class="text-right flex flex-col items-end gap-1">
                    <p class="text-base font-bold text-[#e879f9]">{{ number_format($appt->total_price,0,',','.') }} ₺</p>
                    @if($appt->review)
                    <div class="flex gap-0.5 justify-end">
                        @for($i=1; $i<=5; $i++)<svg class="w-3 h-3 {{ $i<=$appt->review->rating ? 'text-yellow-400':'text-[#32324a]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
                    </div>
                    @endif
                    <a href="{{ route('admin.appointments.show', $appt) }}" target="_blank" class="btn btn-secondary btn-xs text-[10px] py-1 px-2 font-medium inline-flex items-center gap-1 hover:border-purple-500/40 mt-1">
                        <span>👁️</span> Randevu Detayı ↗
                    </a>
                </div>
            </div>

            {{-- B2B Treatment Records / Formül Adımları --}}
            @php
                $record = $customerProfile->treatmentRecords->firstWhere('appointment_id', $appt->id);
            @endphp
            <div x-data="{ showForm: false }" class="mt-4 border-t border-white/5 pt-3">
                @if($record)
                    <div class="space-y-3">
                        <h5 class="text-xs font-bold text-[#e879f9] flex items-center gap-1">
                            <span>📝</span> İşlem Teknik Detayları
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(!empty($record->formula))
                            <div>
                                <p class="text-[10px] text-[#7070a0] uppercase font-bold mb-1">Boya / Stil Formülü</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($record->formula as $f)
                                    <span class="badge badge-purple text-[10px]">{{ $f }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(!empty($record->products_used))
                            <div>
                                <p class="text-[10px] text-[#7070a0] uppercase font-bold mb-1">Kullanılan Ürünler</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($record->products_used as $p)
                                    <span class="badge badge-gray text-[10px]">{{ $p }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        @if($record->notes)
                        <div>
                            <p class="text-[10px] text-[#7070a0] uppercase font-bold mb-1">Teknisyen Notu</p>
                            <p class="text-xs text-[#a0a0c0] leading-relaxed">{{ $record->notes }}</p>
                        </div>
                        @endif

                        @if($record->before_image || $record->after_image)
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2">
                            @if($record->before_image)
                            <div>
                                <p class="text-[9px] text-[#7070a0] mb-1 font-semibold">Önce</p>
                                <div class="aspect-square rounded-xl overflow-hidden bg-white/5 border border-white/5 max-w-[120px]">
                                    <img src="{{ asset('storage/' . $record->before_image) }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                            @endif
                            @if($record->after_image)
                            <div>
                                <p class="text-[9px] text-[#7070a0] mb-1 font-semibold">Sonra</p>
                                <div class="aspect-square rounded-xl overflow-hidden bg-white/5 border border-white/5 max-w-[120px]">
                                    <img src="{{ asset('storage/' . $record->after_image) }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                @elseif($appt->status === 'completed')
                    <!-- Ekleme Butonu -->
                    <button type="button" @click="showForm = !showForm" class="text-xs text-purple-400 hover:text-purple-300 font-semibold flex items-center gap-1 transition-colors">
                        <span x-text="showForm ? '✕ Formu Kapat' : '➕ Teknik Detay / Formül Ekle'"></span>
                    </button>

                    <!-- Inline Ekleme Formu -->
                    <div x-show="showForm" x-transition class="mt-3 bg-white/3 p-4 rounded-xl border border-white/5 space-y-3">
                        <form method="POST" action="{{ route('admin.customers.treatment-records.store', $customerProfile) }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <input type="hidden" name="appointment_id" value="{{ $appt->id }}">
                            <input type="hidden" name="staff_id" value="{{ $appt->staff_id }}">
                            <input type="hidden" name="service_id" value="{{ $appt->services->first()?->service_id }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label text-[10px]">Stil / Boya Formülü (Virgülle ayırın)</label>
                                    <input type="text" name="formula" class="form-input text-xs" placeholder="Örn: 7.1, %6 Oksidan, 30gr">
                                </div>
                                <div>
                                    <label class="form-label text-[10px]">Kullanılan Ürünler (Virgülle ayırın)</label>
                                    <input type="text" name="products_used" class="form-input text-xs" placeholder="Örn: L'Oreal Serie Expert, Olaplex No.3">
                                </div>
                            </div>

                            <div>
                                <label class="form-label text-[10px]">Teknisyen Notu</label>
                                <textarea name="notes" rows="2" class="form-input text-xs" placeholder="Uygulama esnasında alınan notlar..."></textarea>
                                <p class="text-[9px] text-[#7070a0] mt-1">Örn: "Saç uçları kuru, nem bakımı önerildi. Boya 35dk bekletildi."</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label text-[10px]">İşlem Öncesi Fotoğraf</label>
                                    <input type="file" name="before_image" accept="image/*" class="w-full text-xs text-white/50 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                                </div>
                                <div>
                                    <label class="form-label text-[10px]">İşlem Sonrası Fotoğraf</label>
                                    <input type="file" name="after_image" accept="image/*" class="w-full text-xs text-white/50 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                                </div>
                            </div>

                            <div class="flex justify-end pt-1">
                                <button type="submit" class="btn btn-primary btn-sm py-1.5 px-4 text-xs font-bold">
                                    Kaydet 💾
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-[#7070a0]">Randevu geçmişi bulunamadı.</div>
        @endforelse
    </div>
</div>
@endsection
