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
            <div class="flex items-start gap-4 flex-wrap">
                <div class="w-14 h-14 rounded-2xl gradient-brand flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                    {{ substr($appointment->customer->name,0,1) }}
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-white">{{ $appointment->customer->name }}</h2>
                    <p class="text-sm text-[#7070a0]">{{ $appointment->customer->phone }}</p>
                    <div class="flex gap-3 mt-2 flex-wrap text-xs text-[#a0a0c0]">
                        <span>📅 {{ $appointment->appointment_date->format('d M Y, D') }}</span>
                        <span>🕐 {{ substr($appointment->start_time,0,5) }} – {{ substr($appointment->end_time,0,5) }}</span>
                        <span>👤 {{ $appointment->staff->name }}</span>
                    </div>
                </div>
                @php
                $badgeClass = match($appointment->status) { 'pending'=>'badge-warning','confirmed'=>'badge-info','in_progress'=>'badge-purple','completed'=>'badge-success','cancelled'=>'badge-danger','no_show'=>'badge-gray', default=>'badge-gray' };
                $statusLabel = match($appointment->status) { 'pending'=>'Bekliyor','confirmed'=>'Onaylı','in_progress'=>'Devam Ediyor','completed'=>'Tamamlandı','cancelled'=>'İptal','no_show'=>'Gelmedi', default=>$appointment->status };
                @endphp
                <span class="badge {{ $badgeClass }} text-sm">{{ $statusLabel }}</span>
            </div>
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
    </div>

    {{-- Sağ: Aksiyonlar --}}
    <div class="space-y-4">

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
