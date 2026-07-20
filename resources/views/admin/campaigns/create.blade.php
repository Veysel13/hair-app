@extends('layouts.admin')
@section('title','Kampanya Oluştur')
@section('page-title','Kampanya Oluştur')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.campaigns.store') }}" class="space-y-4">
        @csrf
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-white">Genel Bilgiler</h3>

            <div>
                <label class="form-label">Kampanya Adı *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="Örn: Yaz Sezonu İndirimi ☀️" required>
            </div>

            <div>
                <label class="form-label">Açıklama</label>
                <textarea name="description" rows="3" class="form-input" placeholder="Kampanya detayları ve koşulları...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Kampanya Türü *</label>
                    <select name="type" class="form-input" required>
                        <option value="discount">Genel İndirim</option>
                        <option value="birthday">Doğum Günü Kampanyası</option>
                        <option value="win_back">Geri Kazanım Kampanyası (Win Back)</option>
                        <option value="flash">Flaş Kampanya (Saat Kısıtlı)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Kampanya Kodu (Kupon)</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="form-input" placeholder="Örn: YAZ20 (Opsiyonel)">
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-white">İndirim Tanımı</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">İndirim Türü</label>
                    <select name="discount_type" class="form-input">
                        <option value="percentage">Yüzde (%)</option>
                        <option value="fixed">Sabit Tutar (₺)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">İndirim Değeri</label>
                    <input type="number" name="discount_value" value="{{ old('discount_value') }}" min="0" step="0.01" class="form-input" placeholder="Örn: 20">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Minimum Randevu Tutarı (₺)</label>
                    <input type="number" name="min_booking_amount" value="{{ old('min_booking_amount', 0) }}" min="0" step="0.01" class="form-input" placeholder="0.00">
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-white">Koşullar & Kısıtlamalar</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Başlangıç Tarihi *</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at', now()->format('Y-m-d')) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Bitiş Tarihi</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at') }}" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Maksimum Kullanım (Toplam)</label>
                    <input type="number" name="max_uses" value="{{ old('max_uses') }}" min="1" class="form-input" placeholder="Sınırsız">
                </div>
                <div>
                    <label class="form-label">Müşteri Başına Maksimum Kullanım</label>
                    <input type="number" name="max_uses_per_customer" value="{{ old('max_uses_per_customer', 1) }}" min="1" class="form-input">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="w-4 h-4 rounded accent-purple-500" id="is_active">
                <label class="text-sm text-[#a0a0c0] cursor-pointer" for="is_active">Kampanya oluşturulduğunda aktif olsun</label>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </div>
    </form>
</div>
@endsection
