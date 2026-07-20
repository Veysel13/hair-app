@extends('layouts.admin')
@section('title','Finans')
@section('page-title','Finans & Raporlar')
@section('page-subtitle', now()->setMonth($month)->format('F') . ' ' . $year)
@section('content')

{{-- KPI Kartları --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <div class="stat-card border-l-4 border-green-500">
        <p class="text-xs text-[#7070a0] mb-1">Aylık Gelir</p>
        <p class="text-2xl font-bold text-green-400">{{ number_format($monthlyRevenue,0,',','.') }} ₺</p>
    </div>
    <div class="stat-card border-l-4 border-red-500">
        <p class="text-xs text-[#7070a0] mb-1">Aylık Gider</p>
        <p class="text-2xl font-bold text-red-400">{{ number_format($monthlyExpense,0,',','.') }} ₺</p>
    </div>
    <div class="stat-card border-l-4 {{ $netProfit>=0 ? 'border-purple-500':'border-orange-500' }}">
        <p class="text-xs text-[#7070a0] mb-1">Net Kâr</p>
        <p class="text-2xl font-bold {{ $netProfit>=0 ? 'gradient-text':'text-orange-400' }}">{{ number_format($netProfit,0,',','.') }} ₺</p>
    </div>
</div>

{{-- Grafik + Gider Kategorileri --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-5">
    <div class="xl:col-span-2 glass-card rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">Son 12 Ay — Gelir / Gider</h3>
        <div style="height:220px;"><canvas id="financeChart"></canvas></div>
    </div>
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">Gider Dağılımı</h3>
        @forelse($expenseByCategory as $cat => $amount)
        <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
            <span class="text-xs text-[#a0a0c0]">{{ match($cat){'rent'=>'Kira','utilities'=>'Faturalar','salary'=>'Maaş','supplies'=>'Sarf','equipment'=>'Ekipman','marketing'=>'Reklam','tax'=>'Vergi','insurance'=>'Sigorta',default=>'Diğer'} }}</span>
            <span class="text-sm font-semibold text-white">{{ number_format($amount,0,',','.') }} ₺</span>
        </div>
        @empty<p class="text-sm text-[#7070a0] text-center py-4">Gider yok</p>
        @endforelse
    </div>
</div>

{{-- Gider Ekleme + Liste --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <div class="glass-card rounded-2xl p-6" x-data="{open:false}">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-white">Gider Ekle</h3>
            <button @click="open=!open" class="btn btn-secondary btn-sm">{{ '+' }}</button>
        </div>
        <form method="POST" action="{{ route('admin.finance.expenses.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="form-label">Kategori</label>
                <select name="category" class="form-input" required>
                    @foreach(['rent'=>'Kira','utilities'=>'Faturalar','salary'=>'Maaş','supplies'=>'Sarf Malzeme','equipment'=>'Ekipman','marketing'=>'Reklam','tax'=>'Vergi','insurance'=>'Sigorta','other'=>'Diğer'] as $val=>$label)
                    <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="form-label">Açıklama *</label><input type="text" name="description" class="form-input" required placeholder="Açıklama..."></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="form-label">Tutar (₺) *</label><input type="number" name="amount" step="0.01" min="0" class="form-input" required></div>
                <div><label class="form-label">Tarih *</label><input type="date" name="expense_date" value="{{ now()->format('Y-m-d') }}" class="form-input" required></div>
            </div>
            <button type="submit" class="btn btn-primary w-full">Ekle</button>
        </form>
    </div>

    <div class="xl:col-span-2 glass-card rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-white/5">
            <h3 class="text-sm font-semibold text-white">Bu Ayki Giderler</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Tarih</th><th>Kategori</th><th>Açıklama</th><th>Tutar</th><th></th></tr></thead>
                <tbody>
                    @forelse($expenses as $e)
                    <tr>
                        <td class="text-sm text-[#d0d0e0]">{{ $e->expense_date->format('d M') }}</td>
                        <td><span class="badge badge-gray">{{ $e->category_label }}</span></td>
                        <td class="text-sm text-[#d0d0e0]">{{ $e->description }}</td>
                        <td class="text-sm font-semibold text-red-400">{{ number_format($e->amount,0,',','.') }} ₺</td>
                        <td>
                            <form method="POST" action="{{ route('admin.finance.expenses.destroy',$e) }}" onsubmit="return confirm('Sil?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        </td>
                    </tr>
                    @empty<tr><td colspan="5" class="text-center py-8 text-[#7070a0]">Bu ay gider girilmemiş</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = @json($revenueByMonth);
    new Chart(document.getElementById('financeChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: data.map(d => d.label),
            datasets: [
                { label: 'Gelir', data: data.map(d => d.revenue), backgroundColor: 'rgba(34,197,94,0.3)', borderColor: '#22c55e', borderWidth: 2, borderRadius: 4 },
                { label: 'Gider', data: data.map(d => d.expense), backgroundColor: 'rgba(239,68,68,0.3)', borderColor: '#ef4444', borderWidth: 2, borderRadius: 4 },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#a0a0c0', font: { size: 11 } } } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#7070a0', font: { size: 10 } } },
                y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#7070a0', font: { size: 10 }, callback: v => v.toLocaleString('tr-TR')+'₺' } }
            }
        }
    });
});
</script>
@endpush
