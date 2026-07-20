@extends('layouts.admin')
@section('title','Randevular')
@section('page-title','Randevular')
@section('page-subtitle','Tüm randevuları görüntüle ve yönet')

@section('content')

{{-- Filtreler --}}
<div class="glass-card rounded-2xl p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="form-label">Müşteri / Telefon Ara</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="İsim veya telefon...">
        </div>
        <div class="min-w-[140px]">
            <label class="form-label">Durum</label>
            <select name="status" class="form-input">
                <option value="">Tümü</option>
                @foreach(['pending'=>'Bekliyor','confirmed'=>'Onaylı','in_progress'=>'Devam Ediyor','completed'=>'Tamamlandı','cancelled'=>'İptal','no_show'=>'Gelmedi'] as $val=>$label)
                <option value="{{ $val }}" {{ request('status')===$val ? 'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="form-label">Personel</label>
            <select name="staff_id" class="form-input">
                <option value="">Tümü</option>
                @foreach($staff as $s)
                <option value="{{ $s->id }}" {{ request('staff_id')==$s->id ? 'selected':'' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="form-label">Tarih</label>
            <input type="date" name="date" value="{{ request('date') }}" class="form-input">
        </div>
        <button type="submit" class="btn btn-primary">Filtrele</button>
        <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">Sıfırla</a>
        <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary ml-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Yeni Randevu
        </a>
    </form>
</div>

{{-- Tablo --}}
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Müşteri</th>
                    <th>Tarih / Saat</th>
                    <th>Personel</th>
                    <th>Hizmetler</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appt)
                <tr>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg gradient-brand flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ substr($appt->customer->name,0,1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">{{ $appt->customer->name }}</p>
                                <p class="text-xs text-[#7070a0]">{{ $appt->customer->phone }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="text-sm font-medium text-white">{{ $appt->appointment_date->format('d M Y') }}</p>
                        <p class="text-xs text-[#7070a0]">{{ substr($appt->start_time,0,5) }} – {{ substr($appt->end_time,0,5) }}</p>
                    </td>
                    <td class="text-sm text-[#d0d0e0]">{{ $appt->staff->name }}</td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            @foreach($appt->services->take(2) as $s)
                            <span class="badge badge-gray text-xs">{{ Str::limit($s->service->name,20) }}</span>
                            @endforeach
                            @if($appt->services->count() > 2)
                            <span class="badge badge-gray text-xs">+{{ $appt->services->count()-2 }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-sm font-semibold text-white">{{ number_format($appt->total_price,0,',','.') }} ₺</td>
                    <td>
                        @php
                        $badgeClass = match($appt->status) {
                            'pending'     => 'badge-warning',
                            'confirmed'   => 'badge-info',
                            'in_progress' => 'badge-purple',
                            'completed'   => 'badge-success',
                            'cancelled'   => 'badge-danger',
                            'no_show'     => 'badge-gray',
                            default       => 'badge-gray',
                        };
                        $statusLabel = match($appt->status) {
                            'pending'     => 'Bekliyor',
                            'confirmed'   => 'Onaylı',
                            'in_progress' => 'Devam Ediyor',
                            'completed'   => 'Tamamlandı',
                            'cancelled'   => 'İptal',
                            'no_show'     => 'Gelmedi',
                            default       => $appt->status,
                        };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.appointments.show',$appt) }}" class="btn btn-secondary btn-sm btn-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-12 text-[#7070a0]">Randevu bulunamadı</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-white/5">
        {{ $appointments->withQueryString()->links() }}
    </div>
</div>
@endsection
