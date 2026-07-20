@extends('layouts.admin')
@section('title','Stok')
@section('page-title','Stok Yönetimi')
@section('page-subtitle','Ürün ve malzeme takibi')
@section('content')
<div class="flex justify-between items-center mb-4 flex-wrap gap-3">
    @if($lowStockCount > 0)
    <div class="flex items-center gap-2 bg-orange-500/10 border border-orange-500/30 text-orange-400 px-4 py-2 rounded-xl text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <strong>{{ $lowStockCount }}</strong> ürün kritik stok seviyesinde!
    </div>
    @endif
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary ml-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ürün Ekle
    </a>
</div>
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr><th>Ürün</th><th>Kategori</th><th>Stok</th><th>Min Stok</th><th>Alış Fiyatı</th><th>Durum</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <p class="text-sm font-medium text-white">{{ $product->name }}</p>
                        @if($product->brand)<p class="text-xs text-[#7070a0]">{{ $product->brand }}</p>@endif
                    </td>
                    <td><span class="badge badge-gray">{{ $product->category }}</span></td>
                    <td>
                        <span class="{{ $product->isLowStock() ? 'text-orange-400 font-bold':'text-white' }} text-sm">
                            {{ $product->formatted_stock }}
                        </span>
                    </td>
                    <td class="text-sm text-[#7070a0]">{{ $product->min_stock_level }} {{ $product->unit }}</td>
                    <td class="text-sm text-white">{{ $product->purchase_price ? number_format($product->purchase_price,2,',','.').' ₺':'-' }}</td>
                    <td>
                        @if($product->isOutOfStock()) <span class="badge badge-danger">Tükendi</span>
                        @elseif($product->isLowStock()) <span class="badge badge-warning">Kritik</span>
                        @else <span class="badge badge-success">Yeterli</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-1">
                            <a href="{{ route('admin.products.edit',$product) }}" class="btn btn-secondary btn-sm btn-icon">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-12 text-[#7070a0]">Ürün bulunamadı.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
