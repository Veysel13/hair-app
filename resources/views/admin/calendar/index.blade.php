@extends('layouts.admin')
@section('title','Takvim')
@section('page-title','Haftalık Takvim')
@section('page-subtitle', $weekStart->format('d M') . ' – ' . $weekEnd->format('d M Y'))

@section('content')

{{-- Navigasyon --}}
<div class="flex items-center gap-3 mb-4 flex-wrap">
    <a href="{{ route('admin.calendar.index', ['week' => $prevWeek]) }}" class="btn btn-secondary btn-sm">← Önceki Hafta</a>
    <a href="{{ route('admin.calendar.index') }}" class="btn btn-secondary btn-sm">Bu Hafta</a>
    <a href="{{ route('admin.calendar.index', ['week' => $nextWeek]) }}" class="btn btn-secondary btn-sm">Sonraki Hafta →</a>
    <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary btn-sm ml-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Yeni Randevu
    </a>
</div>

{{-- Takvim --}}
<div x-data="{
    selected: null,
    setSelected(appt) { this.selected = appt; },
    close() { this.selected = null; }
}">

{{-- Haftalık Grid --}}
<div class="glass-card rounded-2xl overflow-hidden">
    {{-- Gün Başlıkları --}}
    <div class="grid gap-0 border-b border-white/5" style="grid-template-columns: 100px repeat(7, 1fr);">
        <div class="p-3 text-xs text-[#7070a0] font-semibold"></div>
        @for($i=0; $i<7; $i++)
        @php
            $d = $weekStart->copy()->addDays($i);
            $isToday = $d->isToday();
        @endphp
        <div class="p-3 text-center border-l border-white/5">
            <p class="text-xs text-[#7070a0] uppercase">{{ $d->locale('tr')->isoFormat('ddd') }}</p>
            <p class="text-lg font-bold {{ $isToday ? 'gradient-text' : 'text-white' }}">{{ $d->format('d') }}</p>
            @if($isToday) <div class="w-1.5 h-1.5 rounded-full gradient-brand mx-auto mt-0.5"></div> @endif
        </div>
        @endfor
    </div>

    {{-- Personel Satırları --}}
    @foreach($staff as $s)
    <div class="grid border-b border-white/5 last:border-0 hover:bg-white/[0.01] transition-colors"
         style="grid-template-columns: 100px repeat(7, 1fr); min-height: 80px;">
        {{-- Personel ismi --}}
        <div class="p-3 border-r border-white/5 flex items-start pt-3">
            <div>
                <div class="w-7 h-7 rounded-lg gradient-brand flex items-center justify-center text-white text-xs font-bold mb-1">
                    {{ substr($s->name,0,1) }}
                </div>
                <p class="text-xs font-medium text-white leading-tight">{{ Str::words($s->name, 1, '') }}</p>
                <p class="text-[10px] text-[#7070a0]">{{ $s->title }}</p>
            </div>
        </div>

        {{-- Günlük Slotlar --}}
        @for($i=0; $i<7; $i++)
        @php
            $date = $weekStart->copy()->addDays($i)->toDateString();
            $dayAppts = $calendarData[$date][$s->id] ?? collect();
        @endphp
        <div class="p-1.5 border-l border-white/5 space-y-1">
            @forelse($dayAppts as $appt)
            <button type="button"
                @click="setSelected({{ json_encode(['id'=>$appt->id,'customer'=>$appt->customer->name,'staff'=>$appt->staff->name,'start'=>substr($appt->start_time,0,5),'end'=>substr($appt->end_time,0,5),'status'=>$appt->status,'price'=>number_format($appt->total_price,0,',','.').' ₺','services'=>$appt->services->pluck('service.name')->implode(', ')]) }})"
                class="w-full text-left p-1.5 rounded-lg border text-xs transition-all hover:scale-[1.02]
                {{ match($appt->status) {
                    'confirmed'  => 'bg-blue-500/15 border-blue-500/30 text-blue-300',
                    'pending'    => 'bg-yellow-500/15 border-yellow-500/30 text-yellow-300',
                    'completed'  => 'bg-green-500/15 border-green-500/30 text-green-300',
                    'in_progress'=> 'bg-purple-500/15 border-purple-500/30 text-purple-300',
                    default      => 'bg-white/5 border-white/10 text-[#a0a0c0]',
                } }}">
                <p class="font-semibold truncate">{{ $appt->customer->name }}</p>
                <p class="opacity-75">{{ substr($appt->start_time,0,5) }}–{{ substr($appt->end_time,0,5) }}</p>
            </button>
            @empty
            <div class="h-full min-h-[60px] rounded-lg border border-dashed border-white/5"></div>
            @endforelse
        </div>
        @endfor
    </div>
    @endforeach

    @if($staff->isEmpty())
    <div class="p-12 text-center text-[#7070a0]">Aktif personel bulunamadı.</div>
    @endif
</div>

{{-- Seçilen Randevu Modal --}}
<div x-show="selected" x-transition.opacity @keydown.escape.window="close()"
     class="modal-backdrop" @click.self="close()">
    <div x-show="selected" x-transition class="modal-box" @click.stop>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-white">Randevu Detayı</h3>
            <button @click="close()" class="text-[#7070a0] hover:text-white">✕</button>
        </div>
        <dl class="space-y-3 text-sm" x-show="selected">
            <div class="flex justify-between"><dt class="text-[#7070a0]">Müşteri</dt><dd class="text-white font-medium" x-text="selected?.customer"></dd></div>
            <div class="flex justify-between"><dt class="text-[#7070a0]">Personel</dt><dd class="text-white" x-text="selected?.staff"></dd></div>
            <div class="flex justify-between"><dt class="text-[#7070a0]">Saat</dt><dd class="text-white" x-text="selected?.start + ' – ' + selected?.end"></dd></div>
            <div class="flex justify-between"><dt class="text-[#7070a0]">Hizmetler</dt><dd class="text-white text-right max-w-[200px]" x-text="selected?.services"></dd></div>
            <div class="flex justify-between"><dt class="text-[#7070a0]">Tutar</dt><dd class="text-[#e879f9] font-bold" x-text="selected?.price"></dd></div>
        </dl>
        <div class="mt-5 flex gap-2">
            <a :href="'/admin/appointments/' + selected?.id" class="btn btn-primary flex-1">Detayı Aç</a>
            <button @click="close()" class="btn btn-secondary">Kapat</button>
        </div>
    </div>
</div>

</div>

{{-- Renk Kılavuzu --}}
<div class="flex gap-3 mt-3 flex-wrap">
    <div class="flex items-center gap-1.5 text-xs text-[#7070a0]"><div class="w-3 h-3 rounded bg-blue-500/40"></div> Onaylı</div>
    <div class="flex items-center gap-1.5 text-xs text-[#7070a0]"><div class="w-3 h-3 rounded bg-yellow-500/40"></div> Bekliyor</div>
    <div class="flex items-center gap-1.5 text-xs text-[#7070a0]"><div class="w-3 h-3 rounded bg-purple-500/40"></div> Devam Ediyor</div>
    <div class="flex items-center gap-1.5 text-xs text-[#7070a0]"><div class="w-3 h-3 rounded bg-green-500/40"></div> Tamamlandı</div>
</div>
@endsection
