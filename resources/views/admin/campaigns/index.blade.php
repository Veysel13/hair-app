@extends('layouts.admin')
@section('title','Kampanyalar')
@section('page-title','Kampanyalar')
@section('page-subtitle','İndirim ve promosyon yönetimi')
@section('content')
<div class="flex justify-end mb-4">
    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Kampanya Oluştur
    </a>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($campaigns as $campaign)
    <div class="glass-card rounded-2xl p-5 border {{ $campaign->isActive() ? 'border-purple-500/20':'border-white/5' }} transition-all hover:shadow-lg">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <p class="text-sm font-semibold text-white">{{ $campaign->name }}</p>
                <span class="badge badge-gray text-xs mt-1">{{ $campaign->type_label }}</span>
            </div>
            <span class="badge {{ $campaign->isActive() ? 'badge-success':'badge-gray' }} ml-2 flex-shrink-0">
                {{ $campaign->isActive() ? 'Aktif':'Pasif' }}
            </span>
        </div>
        @if($campaign->description)
        <p class="text-xs text-[#7070a0] mb-3 line-clamp-2">{{ $campaign->description }}</p>
        @endif
        <div class="flex items-center gap-3 mb-4 text-sm">
            @if($campaign->discount_value)
            <div class="px-3 py-1.5 rounded-lg gradient-brand text-white text-xs font-bold">
                @if($campaign->discount_type==='percentage')%{{ $campaign->discount_value }}
                @else{{ number_format($campaign->discount_value,0,',','.') }} ₺
                @endif İndirim
            </div>
            @endif
            <div class="text-xs text-[#7070a0]">{{ $campaign->usages_count }} kullanım</div>
        </div>
        @if($campaign->ends_at)
        <p class="text-xs text-[#7070a0] mb-3">Bitiş: {{ $campaign->ends_at->format('d M Y') }}</p>
        @endif
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.campaigns.toggle',$campaign) }}" class="flex-1">
                @csrf @method('PATCH')
                <button type="submit" class="btn {{ $campaign->is_active ? 'btn-secondary':'btn-primary' }} btn-sm w-full">
                    {{ $campaign->is_active ? 'Pasif Et':'Aktif Et' }}
                </button>
            </form>
            <form method="POST" action="{{ route('admin.campaigns.destroy',$campaign) }}" onsubmit="return confirm('Kampanyayı sil?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm btn-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16 text-[#7070a0]">Henüz kampanya oluşturulmadı.</div>
    @endforelse
</div>
@endsection
