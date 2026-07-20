@extends('layouts.admin')
@section('title','Ürün Düzenle')
@section('page-title','Ürün Düzenle')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- Sol/Orta: Ürün Bilgileri Formu --}}
    <div class="xl:col-span-2 space-y-4">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-semibold text-white">Ürün Detayları</h3>
                
                <div>
                    <label class="form-label">Ürün Adı *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-input" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Marka</label>
                        <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Kategori</label>
                        <input type="text" name="category" value="{{ old('category', $product->category) }}" class="form-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">SKU / Barkod</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="form-input" readonly>
                    </div>
                    <div>
                        <label class="form-label">Birim *</label>
                        <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" class="form-input" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Mevcut Stok (Hızlı Stok Girişinden Değiştirin)</label>
                        <input type="text" value="{{ $product->formatted_stock }}" class="form-input bg-white/5" disabled>
                    </div>
                    <div>
                        <label class="form-label">Minimum Stok Seviyesi *</label>
                        <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}" min="0" step="0.01" class="form-input" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Alış Fiyatı (₺)</label>
                        <input type="number" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" min="0" step="0.01" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Satış Fiyatı (₺)</label>
                        <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="0.01" class="form-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_for_sale" value="0">
                        <input type="checkbox" name="is_for_sale" value="1" {{ old('is_for_sale', $product->is_for_sale) ? 'checked' : '' }} class="w-4 h-4 rounded accent-purple-500" id="is_for_sale">
                        <label class="text-sm text-[#a0a0c0] cursor-pointer" for="is_for_sale">Müşterilere Satılıyor</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded accent-purple-500" id="is_active">
                        <label class="text-sm text-[#a0a0c0] cursor-pointer" for="is_active">Aktif Ürün</label>
                    </div>
                </div>

                <div>
                    <label class="form-label">Notlar</label>
                    <textarea name="notes" rows="3" class="form-input">{{ old('notes', $product->notes) }}</textarea>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">İptal</a>
                <button type="submit" class="btn btn-primary">Güncelle</button>
            </div>
        </form>

        {{-- Stok Hareket Geçmişi --}}
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="p-4 border-b border-white/5">
                <h3 class="text-sm font-semibold text-white">Stok Hareket Geçmişi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>İşlem</th>
                            <th>Miktar</th>
                            <th>Birim Fiyat</th>
                            <th>Not</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->stockMovements as $movement)
                        <tr>
                            <td class="text-sm text-[#d0d0e0]">{{ $movement->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <span class="badge {{ $movement->isEntry() ? 'badge-success' : 'badge-danger' }}">
                                    {{ $movement->type_label }}
                                </span>
                            </td>
                            <td class="text-sm text-white font-medium">
                                {{ $movement->isEntry() ? '+' : '-' }}{{ number_format(abs($movement->quantity), 2, ',', '.') }} {{ $product->unit }}
                            </td>
                            <td class="text-sm text-white">
                                {{ $movement->unit_price ? number_format($movement->unit_price, 2, ',', '.') . ' ₺' : '—' }}
                            </td>
                            <td class="text-xs text-[#a0a0c0] max-w-[200px] truncate" title="{{ $movement->notes }}">{{ $movement->notes }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-6 text-[#7070a0]">Hareket bulunamadı.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sağ: Hızlı Stok Girişi --}}
    <div class="space-y-4">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Hızlı Stok Girişi</h3>
            <form method="POST" action="{{ route('admin.products.addStock', $product) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Eklenecek Miktar *</label>
                    <div class="relative">
                        <input type="number" name="quantity" step="0.01" min="0.01" class="form-input" placeholder="0.00" required>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[#7070a0]">{{ $product->unit }}</span>
                    </div>
                </div>
                <div>
                    <label class="form-label">Birim Alış Fiyatı (₺) (Opsiyonel)</label>
                    <input type="number" name="unit_price" step="0.01" min="0" class="form-input" placeholder="0.00">
                </div>
                <div>
                    <label class="form-label">Not</label>
                    <input type="text" name="notes" class="form-input" placeholder="Fatura no, tedarikçi vb...">
                </div>
                <button type="submit" class="btn btn-primary w-full">Stok Ekle</button>
            </form>
        </div>
    </div>
</div>
@endsection
