@extends('layouts.admin')
@section('title','Personel Düzenle')
@section('page-title','Personel Düzenle')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.staff.update', $staff) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-white">Kişisel Bilgiler</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Ad Soyad *</label>
                    <input type="text" name="name" value="{{ old('name', $staff->name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Unvan</label>
                    <input type="text" name="title" value="{{ old('title', $staff->title) }}" class="form-input" placeholder="Uzman Kuaför">
                </div>
            </div>
            <div>
                <label class="form-label">Hakkında</label>
                <textarea name="bio" rows="3" class="form-input">{{ old('bio', $staff->bio) }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Görüntülenme Sırası</label>
                    <input type="number" name="display_order" value="{{ old('display_order', $staff->display_order) }}" min="0" class="form-input">
                </div>
                <div>
                    <label class="form-label">Varsayılan Prim Oranı (%)</label>
                    <input type="number" name="commission_rate" value="{{ old('commission_rate', $staff->commission_rate ?? 30) }}" min="0" max="100" step="0.5" class="form-input" placeholder="Örn: 30">
                </div>
                <div>
                    <label class="form-label">Durum</label>
                    <select name="is_active" class="form-input">
                        <option value="1" {{ old('is_active', $staff->is_active) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !old('is_active', $staff->is_active) ? 'selected' : '' }}>Pasif</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-3">Verebileceği Hizmetler</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @php
                    $assignedServices = $staff->services->pluck('id')->toArray();
                @endphp
                @foreach($services as $s)
                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-white/10 hover:border-purple-500/30 cursor-pointer transition-colors">
                    <input type="checkbox" name="service_ids[]" value="{{ $s->id }}" {{ in_array($s->id, $assignedServices) ? 'checked' : '' }} class="accent-purple-500 w-4 h-4">
                    <span class="text-sm text-white">{{ $s->name }}</span>
                    <span class="ml-auto text-xs text-[#7070a0]">{{ $s->duration_minutes }}dk</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-primary">Güncelle</button>
        </div>
    </form>
</div>
@endsection
