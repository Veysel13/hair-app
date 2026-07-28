@extends('layouts.admin')
@section('title','Randevu Detay')
@section('page-title','Randevu #' . $appointment->id)
@section('page-subtitle', $appointment->customer->name . ' — ' . $appointment->appointment_date->format('d M Y'))

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

    {{-- Sol: Randevu Detayları --}}
    <div class="xl:col-span-2 space-y-4">

        {{-- Müşteri + Personel Özeti --}}
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl gradient-brand flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                        {{ substr($appointment->customer->name,0,1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-lg font-bold text-white">{{ $appointment->customer->name }}</h2>
                            @if($customerProfile->is_vip)
                            <span class="badge badge-purple text-xs">⭐ VIP</span>
                            @endif
                        </div>
                        <p class="text-sm text-[#7070a0]">{{ $appointment->customer->phone }}</p>
                        <div class="flex gap-3 mt-2 flex-wrap text-xs text-[#a0a0c0]">
                            <span>📅 {{ $appointment->appointment_date->format('d M Y, D') }}</span>
                            <span>🕐 {{ substr($appointment->start_time,0,5) }} – {{ substr($appointment->end_time,0,5) }}</span>
                            <span>👤 {{ $appointment->staff->name }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    @php
                    $badgeClass = match($appointment->status) { 'pending'=>'badge-warning','confirmed'=>'badge-info','in_progress'=>'badge-purple','completed'=>'badge-success','cancelled'=>'badge-danger','no_show'=>'badge-gray', default=>'badge-gray' };
                    $statusLabel = match($appointment->status) { 'pending'=>'Bekliyor','confirmed'=>'Onaylı','in_progress'=>'Devam Ediyor','completed'=>'Tamamlandı','cancelled'=>'İptal','no_show'=>'Gelmedi', default=>$appointment->status };
                    @endphp
                    <span class="badge {{ $badgeClass }} text-sm">{{ $statusLabel }}</span>
                    <a href="{{ route('admin.customers.show', $customerProfile) }}" target="_blank" class="btn btn-secondary btn-sm text-xs font-semibold flex items-center gap-1 hover:border-purple-500/40">
                        <span>👤</span> Müşteri Kartı ↗
                    </a>
                </div>
            </div>
        </div>

        {{-- Müşteri Profili & VIP Notları --}}
        <div class="glass-card rounded-2xl p-6 bg-gradient-to-br from-purple-950/30 via-[#181824] to-[#12121a] border border-purple-500/20">
            <div class="flex items-center justify-between gap-2 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>⭐</span> Müşteri Profili & Notları
                </h3>
                <a href="{{ route('admin.customers.show', $customerProfile) }}" target="_blank" class="text-xs text-purple-400 hover:text-purple-300 font-semibold inline-flex items-center gap-1">
                    Notları Düzenle ↗
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                <div class="bg-white/3 p-2.5 rounded-xl border border-white/5">
                    <p class="text-[10px] text-[#7070a0] uppercase font-bold">Saç Tipi</p>
                    <p class="text-xs font-medium text-white mt-0.5">{{ $customerProfile->hair_type ?? '—' }}</p>
                </div>
                <div class="bg-white/3 p-2.5 rounded-xl border border-white/5">
                    <p class="text-[10px] text-[#7070a0] uppercase font-bold">Saç Rengi</p>
                    <p class="text-xs font-medium text-white mt-0.5">{{ $customerProfile->hair_color ?? '—' }}</p>
                </div>
                <div class="bg-white/3 p-2.5 rounded-xl border border-white/5">
                    <p class="text-[10px] text-[#7070a0] uppercase font-bold">Toplam Ziyaret</p>
                    <p class="text-xs font-bold text-white mt-0.5">{{ $customerProfile->total_visits }} Kez</p>
                </div>
                <div class="bg-white/3 p-2.5 rounded-xl border border-white/5">
                    <p class="text-[10px] text-[#7070a0] uppercase font-bold">Toplam Harcama</p>
                    <p class="text-xs font-bold text-[#e879f9] mt-0.5">{{ number_format($customerProfile->total_spent, 0, ',', '.') }} ₺</p>
                </div>
            </div>

            @if($customerProfile->allergies)
            <div class="mb-3 bg-red-500/10 border border-red-500/20 p-3 rounded-xl">
                <p class="text-[10px] text-red-400 font-bold uppercase mb-0.5">⚠️ Alerji / Hassasiyet</p>
                <p class="text-xs text-red-200">{{ $customerProfile->allergies }}</p>
            </div>
            @endif

            @if($customerProfile->notes)
            <div class="bg-white/3 p-3 rounded-xl border border-white/5">
                <p class="text-[10px] text-[#7070a0] uppercase font-bold mb-1">📌 Müşteri Tercihleri ve Notları (Kahve, Alışkanlıklar vb.)</p>
                <p class="text-xs text-[#d0d0e0] leading-relaxed">{{ $customerProfile->notes }}</p>
            </div>
            @else
            <p class="text-xs text-[#7070a0]">Henüz özel not eklenmemiş. Müşteri kartından dilediğiniz notları girebilirsiniz.</p>
            @endif
        </div>

        {{-- Hizmetler --}}
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Hizmetler</h3>
            <div class="space-y-2">
                @foreach($appointment->services as $item)
                <div class="flex items-center justify-between py-2.5 border-b border-white/5 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-white">{{ $item->service->name }}</p>
                        <p class="text-xs text-[#7070a0]">{{ $item->duration_minutes }} dk • {{ $item->staff?->name ?? $appointment->staff->name }}</p>
                    </div>
                    <p class="text-sm font-semibold text-white">{{ number_format($item->price,0,',','.') }} ₺</p>
                </div>
                @endforeach
                <div class="flex items-center justify-between pt-2">
                    <p class="text-sm font-bold text-white">Toplam</p>
                    <p class="text-base font-bold gradient-text">{{ number_format($appointment->total_price,0,',','.') }} ₺</p>
                </div>
            </div>
        </div>
 
        {{-- Uygulanan Kampanya --}}
        @if($appointment->campaignUsage)
        <div class="glass-card rounded-2xl p-6 bg-gradient-to-br from-purple-500/10 to-pink-500/5 border border-purple-500/20">
            <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-1.5">
                <span>🔥</span> Uygulanan Kampanya
            </h3>
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <h4 class="text-sm font-bold text-white">{{ $appointment->campaignUsage->campaign->name }}</h4>
                    <p class="text-xs text-[#a0a0c0] mt-1">{{ $appointment->campaignUsage->campaign->description }}</p>
                </div>
                <div class="bg-purple-500/10 border border-purple-500/25 px-3 py-1.5 rounded-xl text-right">
                    <p class="text-[10px] text-[#7070a0] font-bold uppercase">KAZANÇ</p>
                    <p class="text-sm font-black text-green-400">{{ number_format($appointment->campaignUsage->discount_amount, 0, ',', '.') }} ₺ İndirim</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Ödemeler --}}
        @if($appointment->payments->count())
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Ödemeler</h3>
            <div class="space-y-2">
                @foreach($appointment->payments as $payment)
                <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                    <div>
                        <p class="text-sm text-white">{{ $payment->method_label }}</p>
                        <p class="text-xs text-[#7070a0]">{{ ucfirst($payment->type) }} • {{ $payment->paid_at?->format('d M Y H:i') }}</p>
                    </div>
                    <span class="badge {{ $payment->isCompleted() ? 'badge-success':'badge-warning' }}">{{ number_format($payment->amount,0,',','.') }} ₺</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Yorum --}}
        @if($appointment->review)
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-3">Müşteri Yorumu</h3>
            <div class="flex gap-1 mb-2">
                @for($i=1; $i<=5; $i++)
                <svg class="w-4 h-4 {{ $i<=$appointment->review->rating ? 'text-yellow-400':'text-[#32324a]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endfor
            </div>
            @if($appointment->review->comment)
            <p class="text-sm text-[#d0d0e0]">{{ $appointment->review->comment }}</p>
            @endif
        </div>
        @endif

        {{-- İşlem Teknik Detayı & Formülü (Reçete / Uygulama Notu) --}}
        @php
            $treatmentRecord = $appointment->treatmentRecords->first() ?? $customerProfile->treatmentRecords->firstWhere('appointment_id', $appointment->id);
        @endphp
        <div x-data="{ showForm: false }" class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between gap-2 mb-4">
                <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                    <span>📝</span> Bu Randevunun İşlem & Formül Kaydı
                </h3>
                <button type="button" @click="showForm = !showForm" class="btn btn-secondary btn-sm text-xs font-semibold">
                    <span x-text="showForm ? '✕ Formu Kapat' : '{{ $treatmentRecord ? '✏️ Formülü Düzenle' : '➕ Formül / Not Ekle' }}'"></span>
                </button>
            </div>

            @if($treatmentRecord)
            <div class="space-y-3 bg-white/3 p-4 rounded-xl border border-white/5 mb-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!empty($treatmentRecord->formula))
                    <div>
                        <p class="text-[10px] text-[#7070a0] uppercase font-bold mb-1">Stil / Boya Formülü</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($treatmentRecord->formula as $f)
                            <span class="badge badge-purple text-xs font-medium">{{ $f }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!empty($treatmentRecord->products_used))
                    <div>
                        <p class="text-[10px] text-[#7070a0] uppercase font-bold mb-1">Kullanılan Ürünler</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($treatmentRecord->products_used as $p)
                            <span class="badge badge-gray text-xs font-medium">{{ $p }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                @if($treatmentRecord->notes)
                <div>
                    <p class="text-[10px] text-[#7070a0] uppercase font-bold mb-1">Teknisyen / Operasyon Notu</p>
                    <p class="text-xs text-[#d0d0e0] leading-relaxed">{{ $treatmentRecord->notes }}</p>
                </div>
                @endif

                @if($treatmentRecord->before_image || $treatmentRecord->after_image)
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                    @if($treatmentRecord->before_image)
                    <div>
                        <p class="text-[10px] text-[#7070a0] mb-1 font-bold">Önce</p>
                        <div class="aspect-square rounded-xl overflow-hidden bg-white/5 border border-white/5">
                            <img src="{{ asset('storage/' . $treatmentRecord->before_image) }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                    @endif
                    @if($treatmentRecord->after_image)
                    <div>
                        <p class="text-[10px] text-[#7070a0] mb-1 font-bold">Sonra</p>
                        <div class="aspect-square rounded-xl overflow-hidden bg-white/5 border border-white/5">
                            <img src="{{ asset('storage/' . $treatmentRecord->after_image) }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @else
            <p class="text-xs text-[#7070a0] mb-3">Bu randevuya henüz işlem formülü veya not eklenmedi. İşlem tamamlandıktan sonra veya esnasında aşağıdan formül girebilirsiniz.</p>
            @endif

            {{-- Formül Ekleme / Güncelleme Formu --}}
            <div x-show="showForm" x-transition class="bg-white/3 p-4 rounded-xl border border-white/5 space-y-3 mt-3">
                <form method="POST" action="{{ route('admin.customers.treatment-records.store', $customerProfile) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                    <input type="hidden" name="staff_id" value="{{ $appointment->staff_id }}">
                    <input type="hidden" name="service_id" value="{{ $appointment->services->first()?->service_id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label text-[10px]">Stil / Boya Formülü (Virgülle ayırın)</label>
                            <input type="text" name="formula" value="{{ $treatmentRecord && is_array($treatmentRecord->formula) ? implode(', ', $treatmentRecord->formula) : '' }}" class="form-input text-xs" placeholder="Örn: Koleston 7.1, %6 Oksidan, 30gr">
                        </div>
                        <div>
                            <label class="form-label text-[10px]">Kullanılan Ürünler (Virgülle ayırın)</label>
                            <input type="text" name="products_used" value="{{ $treatmentRecord && is_array($treatmentRecord->products_used) ? implode(', ', $treatmentRecord->products_used) : '' }}" class="form-input text-xs" placeholder="Örn: L'Oreal Serie Expert, Olaplex No.3">
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-[10px]">Teknisyen / Operasyon Notu</label>
                        <textarea name="notes" rows="2" class="form-input text-xs" placeholder="Uygulama esnasında alınan notlar (örn: Saç uçları kuru, 35dk bekletildi)...">{{ $treatmentRecord?->notes }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                            Kaydet ve Müşteri Kartıyla Eşitle 💾
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sağ: Aksiyonlar --}}
    <div class="space-y-4">

        {{-- Adisyon Kapatma ve Ödeme Alma Paneli --}}
        <div class="glass-card rounded-2xl p-6 bg-gradient-to-br from-green-950/40 via-[#161622] to-[#12121a] border border-green-500/30" x-data="{ method: '{{ $appointment->payment_method ?? 'cash' }}' }">
            <div class="flex items-center justify-between gap-2 mb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>💳</span> Adisyon Kapat & Ödeme Al
                </h3>
                @if($appointment->status === 'completed')
                <span class="badge badge-success text-[10px]">Adisyon Kapalı</span>
                @endif
            </div>

            <p class="text-xs text-[#a0a0c0] mb-4">
                Ödeme alma yöntemini seçin, nakit/kart dağılımını ve bahşişi girerek randevuyu tamamlayın.
            </p>

            <form method="POST" action="{{ route('admin.appointments.checkout', $appointment) }}" class="space-y-4">
                @csrf

                {{-- Ödeme Yöntemi Seçimi --}}
                <div>
                    <label class="form-label text-[11px]">Ödeme Yöntemi</label>
                    <div class="grid grid-cols-3 gap-1.5 p-1 rounded-xl bg-white/5 border border-white/5 text-xs font-semibold">
                        <button type="button" @click="method = 'cash'" :class="method === 'cash' ? 'bg-green-600 text-white shadow-md' : 'text-[#a0a0c0] hover:text-white'" class="py-2 rounded-lg transition-all text-center">
                            💵 Nakit
                        </button>
                        <button type="button" @click="method = 'card'" :class="method === 'card' ? 'bg-purple-600 text-white shadow-md' : 'text-[#a0a0c0] hover:text-white'" class="py-2 rounded-lg transition-all text-center">
                            💳 Kredi Kartı
                        </button>
                        <button type="button" @click="method = 'split'" :class="method === 'split' ? 'bg-blue-600 text-white shadow-md' : 'text-[#a0a0c0] hover:text-white'" class="py-2 rounded-lg transition-all text-center">
                            🔀 Parçalı
                        </button>
                    </div>
                    <input type="hidden" name="payment_method" :value="method">
                </div>

                {{-- Parçalı Ödeme Girdileri --}}
                <div x-show="method === 'split'" x-transition class="grid grid-cols-2 gap-3 bg-white/3 p-3 rounded-xl border border-white/5">
                    <div>
                        <label class="form-label text-[10px]">Alınan Nakit (₺)</label>
                        <input type="number" name="cash_amount" value="{{ old('cash_amount', $appointment->cash_amount > 0 ? $appointment->cash_amount : $appointment->total_price / 2) }}" step="0.5" min="0" class="form-input text-xs">
                    </div>
                    <div>
                        <label class="form-label text-[10px]">Alınan Kart (₺)</label>
                        <input type="number" name="card_amount" value="{{ old('card_amount', $appointment->card_amount > 0 ? $appointment->card_amount : $appointment->total_price / 2) }}" step="0.5" min="0" class="form-input text-xs">
                    </div>
                </div>

                {{-- Bahşiş & Fiş Notu --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-[11px]">Bahşiş / Tip (₺)</label>
                        <input type="number" name="tip_amount" value="{{ old('tip_amount', $appointment->tip_amount ?? 0) }}" step="1" min="0" class="form-input text-xs" placeholder="Örn: 50">
                    </div>
                    <div>
                        <label class="form-label text-[11px]">Fiş / Kasa Notu</label>
                        <input type="text" name="internal_notes" value="{{ old('internal_notes', $appointment->internal_notes) }}" class="form-input text-xs" placeholder="Örn: Fatura kesildi">
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-white/3 border border-white/5 flex items-center justify-between text-xs">
                    <span class="text-[#a0a0c0]">Ödenecek Hizmet Bedeli:</span>
                    <span class="text-base font-bold text-green-400">{{ number_format($appointment->total_price, 2, ',', '.') }} ₺</span>
                </div>

                <button type="submit" class="btn btn-primary w-full py-3 text-xs font-bold bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 shadow-lg shadow-green-600/30">
                    <span>⚡</span> Adisyonu Kapat ve Kasa Hesabına İşle
                </button>
            </form>
        </div>

        {{-- Durum Güncelle --}}
        @if(!in_array($appointment->status,['completed','cancelled','no_show']))
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Durum Güncelle</h3>
            <form method="POST" action="{{ route('admin.appointments.status', $appointment) }}" class="space-y-3">
                @csrf @method('PATCH')
                <select name="status" class="form-input">
                    @foreach(['confirmed'=>'Onayla','in_progress'=>'Başladı','completed'=>'Tamamlandı','cancelled'=>'İptal Et','no_show'=>'Gelmedi'] as $val=>$label)
                    <option value="{{ $val }}" {{ $appointment->status===$val ? 'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="text" name="reason" class="form-input" placeholder="İptal sebebi (opsiyonel)">
                <button type="submit" class="btn btn-primary w-full">Güncelle</button>
            </form>
        </div>
        @endif

        {{-- Randevu Bilgileri --}}
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Bilgiler</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-[#7070a0]">Kaynak</dt>
                    <dd class="text-white font-medium capitalize">{{ $appointment->source }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#7070a0]">Süre</dt>
                    <dd class="text-white font-medium">{{ $appointment->total_duration }} dk</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#7070a0]">Kapora</dt>
                    <dd>
                        @if($appointment->deposit_paid)
                        <span class="badge badge-success">Ödendi</span>
                        @else
                        <span class="badge badge-warning">Ödenmedi</span>
                        @endif
                    </dd>
                </div>
                @if($appointment->notes)
                <div>
                    <dt class="text-[#7070a0] mb-1">Not</dt>
                    <dd class="text-[#d0d0e0]">{{ $appointment->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary w-full">← Listeye Dön</a>
    </div>
</div>
@endsection
