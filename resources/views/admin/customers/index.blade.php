@extends('layouts.admin')
@section('title','Müşteriler')
@section('page-title','Müşteriler')
@section('page-subtitle','CRM — Müşteri veritabanı')
@section('content')
<div class="glass-card rounded-2xl p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="form-label">Ara</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="İsim, telefon veya e-posta...">
        </div>
        <label class="flex items-center gap-2 pb-1 cursor-pointer">
            <input type="checkbox" name="vip" value="1" {{ request('vip') ? 'checked':'' }} class="w-4 h-4 accent-purple-500">
            <span class="text-sm text-[#a0a0c0]">Sadece VIP</span>
        </label>
        <label class="flex items-center gap-2 pb-1 cursor-pointer">
            <input type="checkbox" name="inactive" value="1" {{ request('inactive') ? 'checked':'' }} class="w-4 h-4 accent-yellow-500">
            <span class="text-sm text-[#a0a0c0]">3+ Ay Gelmeyenler</span>
        </label>
        <button type="submit" class="btn btn-primary">Filtrele</button>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Sıfırla</a>
    </form>
</div>
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Müşteri</th>
                    <th>Son Ziyaret</th>
                    <th>Toplam Ziyaret</th>
                    <th>Toplam Harcama</th>
                    <th>Durum</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($profiles as $profile)
                <tr>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg gradient-brand flex items-center justify-center text-white text-xs font-bold">{{ substr($profile->customer->name,0,1) }}</div>
                            <div>
                                <p class="text-sm font-medium text-white flex items-center gap-1">
                                    {{ $profile->customer->name }}
                                    @if($profile->is_vip) <span class="text-yellow-400 text-xs">⭐</span> @endif
                                </p>
                                <p class="text-xs text-[#7070a0]">{{ $profile->customer->phone }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="text-sm text-[#d0d0e0]">{{ $profile->last_visit_at ? $profile->last_visit_at->format('d M Y') : '—' }}</td>
                    <td class="text-sm text-white font-medium">{{ $profile->total_visits }}</td>
                    <td class="text-sm font-semibold text-[#e879f9]">{{ number_format($profile->total_spent,0,',','.') }} ₺</td>
                    <td>
                        @if($profile->days_since_last_visit > 90)
                        <span class="badge badge-warning">{{ $profile->days_since_last_visit }}g önce</span>
                        @elseif($profile->is_vip)
                        <span class="badge badge-purple">VIP</span>
                        @else
                        <span class="badge badge-success">Aktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.customers.show',$profile) }}" class="btn btn-secondary btn-sm">Profil</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-12 text-[#7070a0]">Müşteri bulunamadı</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-white/5">{{ $profiles->withQueryString()->links() }}</div>
</div>
@endsection
