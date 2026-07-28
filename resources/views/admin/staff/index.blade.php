@extends('layouts.admin')
@section('title','Personel')
@section('page-title','Personel Yönetimi')
@section('page-subtitle','Çalışanları görüntüle, ekle ve yönet')
@section('content')
<div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
    <div>
        @if($business->enable_commission)
        <a href="{{ route('admin.staff.commissions') }}" class="btn btn-secondary text-xs font-bold border-purple-500/30 text-purple-300 hover:bg-purple-600/20">
            <span>💰</span> Prim & Hak Ediş Raporunu İncele ↗
        </a>
        @else
        <a href="{{ route('admin.settings.edit') }}" class="text-xs text-[#7070a0] hover:text-purple-300 transition-colors">
            ℹ️ Personel prim sistemini aktifleştirmek için tıklayın.
        </a>
        @endif
    </div>

    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Personel Ekle
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($staff as $s)
    @php $stat = $stats[$s->id] ?? []; @endphp
    <div class="glass-card rounded-2xl p-6 hover:border-purple-500/20 border border-transparent transition-all">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl gradient-brand flex items-center justify-center text-white text-lg font-bold">
                    {{ substr($s->name,0,1) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">{{ $s->name }}</p>
                    <p class="text-xs text-[#7070a0]">{{ $s->title }}</p>
                </div>
            </div>
            <div class="flex flex-col items-end gap-1">
                <span class="badge {{ $s->is_active ? 'badge-success':'badge-gray' }}">{{ $s->is_active ? 'Aktif':'Pasif' }}</span>
                @if($business->enable_commission)
                <span class="badge badge-purple text-[10px]" title="Prim Oranı">💰 %{{ number_format($s->commission_rate, 0) }} Prim</span>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="text-center p-2 rounded-xl bg-white/3">
                <p class="text-base font-bold text-white">{{ $stat['total_appointments'] ?? 0 }}</p>
                <p class="text-[10px] text-[#7070a0]">Toplam</p>
            </div>
            <div class="text-center p-2 rounded-xl bg-white/3">
                <p class="text-base font-bold text-white">{{ $stat['this_month'] ?? 0 }}</p>
                <p class="text-[10px] text-[#7070a0]">Bu Ay</p>
            </div>
            <div class="text-center p-2 rounded-xl bg-white/3">
                <p class="text-base font-bold text-yellow-400">{{ $stat['avg_rating'] ?? '—' }}</p>
                <p class="text-[10px] text-[#7070a0]">Puan</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.staff.edit',$s) }}" class="btn btn-secondary btn-sm flex-1">Düzenle</a>
            <form method="POST" action="{{ route('admin.staff.destroy',$s) }}" onsubmit="return confirm('Personeli silmek istiyor musunuz?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm btn-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16 text-[#7070a0]">Henüz personel eklenmedi.</div>
    @endforelse
</div>
@endsection
