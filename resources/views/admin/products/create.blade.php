@extends('layouts.admin')
@section('title','Ürün Ekle')
@section('page-title','Ürün Ekle')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-4">
        @csrf
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-white">Ürün Detayları</h3>
            
            <div>
                <label class="form-label">Ürün Adı *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="Örn: Saç Bakım Şampuanı" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Marka</label>
                    <input type="text" name="brand" value="{{ old('brand') }}" class="form-input" placeholder="Örn: L'Oreal">
                </div>
                <div>
                    <label class="form-label">Kategori</label>
                    <input type="text" name="category" value="{{ old('category') }}" class="form-input" placeholder="Örn: Şampuan, Boya">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">SKU / Barkod</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" class="form-input" placeholder="Barkod numarası...">
                </div>
                <div>
                    <label class="form-label">Birim *</label>
                    <input type="text" name="unit" value="{{ old('unit', 'adet') }}" class="form-input" placeholder="Örn: adet, ml, paket" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Mevcut Stok *</label>
                    <input type="number" name="current_stock" value="{{ old('current_stock', 0) }}" min="0" step="0.01" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Minimum Stok Seviyesi *</label>
                    <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 5) }}" min="0" step="0.01" class="form-input" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Alış Fiyatı (₺)</label>
                    <input type="number" name="purchase_price" value="{{ old('purchase_price') }}" min="0" step="0.01" class="form-input" placeholder="0.00">
                </div>
                <div>
                    <label class="form-label">Satış Fiyatı (₺)</label>
                    <input type="number" name="sale_price" value="{{ old('sale_price') }}" min="0" step="0.01" class="form-input" placeholder="0.00">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_for_sale" value="0">
                <input type="checkbox" name="is_for_sale" value="1" {{ old('is_for_sale') ? 'checked' : '' }} class="w-4 h-4 rounded accent-purple-500" id="is_for_sale">
                <label class="text-sm text-[#a0a0c0] cursor-pointer" for="is_for_sale">Bu ürün müşterilere doğrudan satılıyor (Perakende)</label>
            </div>

            <div>
                <label class="form-label">Notlar</label>
                <textarea name="notes" rows="3" class="form-input" placeholder="Ürün hakkında ek notlar...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </div>
    </form>
</div>
@endsection
