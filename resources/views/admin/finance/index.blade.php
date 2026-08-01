@extends('layouts.admin')
@section('title','Finans & Kasa Takibi')
@section('page-title','Finans, Kasa & Gider Takibi')
@section('page-subtitle', now()->setMonth($month)->format('F') . ' ' . $year . ' — Gün Sonu Kasası ve Net Kâr Analizi')

@section('content')

{{-- ── 1. GÜN SONU KASASI & NAKİT/KART DAĞILIMI ── --}}
<div class="glass-card rounded-2xl p-6 bg-gradient-to-r from-purple-950/40 via-[#161622] to-[#12121a] border border-purple-500/20 mb-6">
    <div class="flex items-center justify-between gap-4 mb-4 flex-wrap">
        <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>🏢</span> Gün Sonu Kasa Özeti (Bugün — {{ now()->format('d.m.Y') }})
            </h3>
            <p class="text-xs text-[#a0a0c0]">Nakit, kredi kartı, bahşiş ve günlük harcama dengesi.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="badge badge-purple text-xs">Canlı Kasa Dengesi</span>
            <a href="{{ route('admin.finance.z-report') }}" target="_blank" class="btn btn-secondary text-xs font-bold border-purple-500/30 text-purple-300 hover:bg-purple-600/20">
                <span>🖨️</span> Gün Sonu Z-Raporu Al & Yazdır ↗
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white/3 p-3.5 rounded-xl border border-white/5 space-y-1">
            <p class="text-[10px] text-[#7070a0] font-bold uppercase">💵 Bugünkü Nakit Kasa</p>
            <p class="text-lg font-bold text-green-400">{{ number_format($todayCash, 0, ',', '.') }} ₺</p>
        </div>

        <div class="bg-white/3 p-3.5 rounded-xl border border-white/5 space-y-1">
            <p class="text-[10px] text-[#7070a0] font-bold uppercase">💳 Bugünkü Kart Kasa</p>
            <p class="text-lg font-bold text-purple-400">{{ number_format($todayCard, 0, ',', '.') }} ₺</p>
        </div>

        <div class="bg-white/3 p-3.5 rounded-xl border border-white/5 space-y-1">
            <p class="text-[10px] text-[#7070a0] font-bold uppercase">🎁 Toplanan Bahşiş</p>
            <p class="text-lg font-bold text-yellow-400">{{ number_format($todayTip, 0, ',', '.') }} ₺</p>
        </div>

        <div class="bg-white/3 p-3.5 rounded-xl border border-white/5 space-y-1">
            <p class="text-[10px] text-[#7070a0] font-bold uppercase">📉 Bugünkü Giderler</p>
            <p class="text-lg font-bold text-red-400">{{ number_format($todayExpense, 0, ',', '.') }} ₺</p>
        </div>

        <div class="col-span-2 sm:col-span-1 bg-gradient-to-br from-green-900/30 to-emerald-900/20 p-3.5 rounded-xl border border-green-500/30 space-y-1">
            <p class="text-[10px] text-green-300 font-bold uppercase">💰 Net Kasa Bakiye</p>
            <p class="text-lg font-bold text-green-300">{{ number_format($todayNetCash, 0, ',', '.') }} ₺</p>
        </div>
    </div>
</div>

{{-- ── 2. AYLIK KPI KARTLARI ── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="stat-card border-l-4 border-green-500">
        <p class="text-xs text-[#7070a0] mb-1">Aylık Toplam Ciro</p>
        <p class="text-2xl font-bold text-green-400">{{ number_format($monthlyRevenue, 0, ',', '.') }} ₺</p>
        <div class="flex gap-3 text-[11px] text-[#a0a0c0] mt-2">
            <span>Nakit: {{ number_format($monthlyCash, 0, ',', '.') }}₺</span>
            <span>Kart: {{ number_format($monthlyCard, 0, ',', '.') }}₺</span>
        </div>
    </div>

    <div class="stat-card border-l-4 border-red-500">
        <p class="text-xs text-[#7070a0] mb-1">Aylık Toplam Gider</p>
        <p class="text-2xl font-bold text-red-400">{{ number_format($monthlyExpense, 0, ',', '.') }} ₺</p>
        <p class="text-[11px] text-[#a0a0c0] mt-2">{{ $expenses->count() }} Kayıtlı Gider İşlemi</p>
    </div>

    <div class="stat-card border-l-4 {{ $netProfit>=0 ? 'border-purple-500' : 'border-orange-500' }}">
        <p class="text-xs text-[#7070a0] mb-1">Aylık Net Kâr / Zarar</p>
        <p class="text-2xl font-bold {{ $netProfit>=0 ? 'gradient-text' : 'text-orange-400' }}">{{ number_format($netProfit, 0, ',', '.') }} ₺</p>
        <p class="text-[11px] text-[#a0a0c0] mt-2">Kâr Marjı: {{ $monthlyRevenue > 0 ? number_format(($netProfit / $monthlyRevenue) * 100, 1) : 0 }}%</p>
    </div>
</div>

{{-- ── 3. GRAFİK & GİDER DAĞILIMI ── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
    <div class="xl:col-span-2 glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-white">Son 12 Ay — Gelir vs Gider vs Net Kâr Trendi</h3>
            <span class="text-xs text-[#7070a0]">Chart.js Canlı Grafik</span>
        </div>
        <div style="height:250px;"><canvas id="financeChart"></canvas></div>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">Gider Kategorileri Dağılımı</h3>
        <div class="space-y-2">
            @forelse($expenseByCategory as $cat => $amount)
            @php
            $percentage = $monthlyExpense > 0 ? ($amount / $monthlyExpense) * 100 : 0;
            $catLabel = match($cat){ 'rent'=>'Kira', 'utilities'=>'Faturalar', 'salary'=>'Maaş / Avans', 'supplies'=>'Sarf Malzeme (Boya vb.)', 'equipment'=>'Ekipman', 'marketing'=>'Reklam & Pazarlama', 'tax'=>'Vergi & Harç', 'insurance'=>'Sigorta', default=>'Diğer Harcama' };
            @endphp
            <div>
                <div class="flex items-center justify-between py-1 text-xs">
                    <span class="text-[#d0d0e0]">{{ $catLabel }}</span>
                    <span class="font-bold text-white">{{ number_format($amount, 0, ',', '.') }} ₺ <span class="text-[10px] text-[#7070a0]">({{ number_format($percentage, 0) }}%)</span></span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-white/5 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full" style="width: {{ $percentage }}%;"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-[#7070a0] text-center py-6">Bu ay için henüz gider kaydı yok.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ── 4. GİDER DEFTERİ: YENİ GİDER EKLE & GİDER LİSTESİ ── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <div class="glass-card rounded-2xl p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-white/5 pb-3">
            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                <span>📒</span> Gider Defterine İşle
            </h3>
            <span class="badge badge-purple text-[10px]">Hızlı Giriş</span>
        </div>

        <form method="POST" action="{{ route('admin.finance.expenses.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="form-label">Kategori *</label>
                <select name="category" class="form-input" required>
                    @foreach([
                        'rent' => '🏢 Kira',
                        'utilities' => '💡 Faturalar (Elektrik, Su, İnternet)',
                        'salary' => '👨‍ Mutfak / Çay / Kahve İkramları',
                        'supplies' => '🧪 Sarf Malzemesi (Boya, Şampuan, Havlu)',
                        'equipment' => '✂️ Ekipman & Cihaz',
                        'marketing' => '📣 Reklam & Pazarlama',
                        'tax' => '⚖️ Vergi & Harç',
                        'insurance' => '🛡️ Sigorta',
                        'other' => '📌 Diğer Giderler'
                    ] as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Açıklama *</label>
                <input type="text" name="description" class="form-input text-xs" required placeholder="Örn: 2 Koli Filtre Kahve ve Süt">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Tutar (₺) *</label>
                    <input type="number" name="amount" step="0.5" min="0" class="form-input text-xs" required placeholder="0.00">
                </div>
                <div>
                    <label class="form-label">Tarih *</label>
                    <input type="date" name="expense_date" value="{{ now()->format('Y-m-d') }}" class="form-input text-xs" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full py-2.5 text-xs font-bold bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500">
                <span>➕</span> Gideri Kaydet ve Kasa Bakiye Düş
            </button>
        </form>
    </div>

    <div class="xl:col-span-2 glass-card rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-white">Bu Ayki Gider Kayıtları</h3>
            <span class="text-xs text-[#7070a0]">Toplam {{ $expenses->count() }} Kalem</span>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Kategori</th>
                        <th>Açıklama</th>
                        <th>Tutar</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $e)
                    <tr>
                        <td class="text-xs text-[#d0d0e0] font-medium">{{ $e->expense_date->format('d M Y') }}</td>
                        <td><span class="badge badge-gray text-[10px]">{{ $e->category_label }}</span></td>
                        <td class="text-xs text-[#d0d0e0]">{{ $e->description }}</td>
                        <td class="text-xs font-bold text-red-400">{{ number_format($e->amount, 2, ',', '.') }} ₺</td>
                        <td>
                            <form method="POST" action="{{ route('admin.finance.expenses.destroy', $e) }}" onsubmit="return confirm('Gider kaydını silmek istediğinize emin misiniz?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Sil">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-[#7070a0] text-xs">Bu ay henüz gider girilmedi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function initFinanceChart() {
    if (typeof Chart === 'undefined') return;
    const canvas = document.getElementById('financeChart');
    if (!canvas) return;
    const data = @json($revenueByMonth);
    const ctx = canvas.getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.label),
            datasets: [
                { 
                    label: 'Gelir (Ciro)', 
                    data: data.map(d => d.revenue), 
                    backgroundColor: 'rgba(34,197,94,0.3)', 
                    borderColor: '#22c55e', 
                    borderWidth: 2, 
                    borderRadius: 4 
                },
                { 
                    label: 'Gider', 
                    data: data.map(d => d.expense), 
                    backgroundColor: 'rgba(239,68,68,0.3)', 
                    borderColor: '#ef4444', 
                    borderWidth: 2, 
                    borderRadius: 4 
                },
                { 
                    label: 'Net Kâr', 
                    data: data.map(d => d.profit), 
                    type: 'line',
                    borderColor: '#c084fc', 
                    backgroundColor: 'rgba(192,132,252,0.1)',
                    borderWidth: 3, 
                    tension: 0.3,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    labels: { color: '#a0a0c0', font: { size: 11 } } 
                } 
            },
            scales: {
                x: { 
                    grid: { color: 'rgba(255,255,255,0.04)' }, 
                    ticks: { color: '#7070a0', font: { size: 10 } } 
                },
                y: { 
                    grid: { color: 'rgba(255,255,255,0.04)' }, 
                    ticks: { 
                        color: '#7070a0', 
                        font: { size: 10 }, 
                        callback: v => v.toLocaleString('tr-TR') + ' ₺' 
                    } 
                }
            }
        }
    });
}
document.addEventListener('chartjs-loaded', initFinanceChart);
document.addEventListener('DOMContentLoaded', initFinanceChart);
</script>
@endpush
