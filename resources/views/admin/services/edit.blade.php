@extends('layouts.admin')
@section('title','Hizmet Düzenle')
@section('page-title','Hizmet Düzenle')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-white">Hizmet Detayları</h3>
            
            <div>
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-input">
                    <option value="">Kategori Yok</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $service->category_id)==$cat->id ? 'selected':'' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Hizmet Adı *</label>
                <input type="text" name="name" value="{{ old('name', $service->name) }}" class="form-input" placeholder="Örn: Klasik Saç Kesimi" required>
            </div>

            <div>
                <label class="form-label">Açıklama</label>
                <textarea name="description" rows="3" class="form-input" placeholder="Hizmet detayları ve açıklaması...">{{ old('description', $service->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Süre (Dakika) *</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes) }}" min="5" max="480" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Fiyat (₺) *</label>
                    <input type="number" name="price" value="{{ old('price', $service->price) }}" min="0" step="0.01" class="form-input" placeholder="0.00" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Görüntülenme Sırası</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $service->sort_order) }}" min="0" class="form-input w-32">
                </div>
                <div>
                    <label class="form-label">Durum</label>
                    <select name="is_active" class="form-input">
                        <option value="1" {{ old('is_active', $service->is_active) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !old('is_active', $service->is_active) ? 'selected' : '' }}>Pasif</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-3">Bu Hizmeti Verebilen Personel</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @php
                    $assignedStaff = $service->staff->pluck('id')->toArray();
                @endphp
                @foreach($staff as $s)
                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-white/10 hover:border-purple-500/30 cursor-pointer transition-colors">
                    <input type="checkbox" name="staff_ids[]" value="{{ $s->id }}" {{ in_array($s->id, $assignedStaff) ? 'checked' : '' }} class="accent-purple-500 w-4 h-4">
                    <span class="text-sm text-white">{{ $s->name }}</span>
                    <span class="ml-auto text-xs text-[#7070a0]">{{ $s->title }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-primary">Güncelle</button>
        </div>
    </form>
</div>
@endsection
