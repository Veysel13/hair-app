@extends('layouts.admin')
@section('title','Hizmetler')
@section('page-title','Hizmet Tanımları')
@section('page-subtitle','Kategori ve hizmet listesi')

@section('content')
<div class="flex justify-end mb-4 gap-2">
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Hizmet Ekle
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- Sol: Hizmet Kategorileri listesi --}}
    <div class="space-y-4">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Hizmet Kategorileri</h3>
            <div class="space-y-2">
                @forelse($categories as $category)
                <div class="flex items-center justify-between p-3 rounded-xl bg-white/3 border border-white/5">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">{{ $category->icon ?: '📁' }}</span>
                        <div>
                            <p class="text-sm font-semibold text-white">{{ $category->name }}</p>
                            <p class="text-[10px] text-[#7070a0]">{{ $category->services->count() }} Hizmet</p>
                        </div>
                    </div>
                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $category->color ?: '#a855f7' }}"></span>
                </div>
                @empty
                <p class="text-sm text-[#7070a0] text-center py-4">Kategori bulunamadı.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Sağ: Hizmet Tanımları Tablosu --}}
    <div class="xl:col-span-2 glass-card rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-white/5">
            <h3 class="text-sm font-semibold text-white">Hizmet Listesi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Hizmet</th>
                        <th>Kategori</th>
                        <th>Süre</th>
                        <th>Fiyat</th>
                        <th>Personel</th>
                        <th>Durum</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $s)
                    <tr>
                        <td>
                            <p class="text-sm font-medium text-white">{{ $s->name }}</p>
                            @if($s->description)<p class="text-xs text-[#7070a0] truncate max-w-[200px]">{{ $s->description }}</p>@endif
                        </td>
                        <td>
                            @if($s->category)
                            <span class="badge badge-gray" style="border-left: 3px solid {{ $s->category->color }}">
                                {{ $s->category->name }}
                            </span>
                            @else
                            <span class="badge badge-gray">Genel</span>
                            @endif
                        </td>
                        <td class="text-sm text-[#d0d0e0]">{{ $s->duration_minutes }} dk</td>
                        <td class="text-sm font-semibold text-[#e879f9]">{{ number_format($s->price,0,',','.') }} ₺</td>
                        <td>
                            <span class="text-xs text-[#a0a0c0]">{{ $s->staff->count() }} Kişi</span>
                        </td>
                        <td>
                            <span class="badge {{ $s->is_active ? 'badge-success' : 'badge-gray' }}">
                                {{ $s->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.services.edit',$s) }}" class="btn btn-secondary btn-sm btn-icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.services.destroy',$s) }}" onsubmit="return confirm('Silmek istiyor musunuz?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-12 text-[#7070a0]">Hizmet bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
