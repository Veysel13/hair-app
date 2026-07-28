@extends('layouts.admin')
@section('title', 'Personel Prim & Hak Ediş Raporu')
@section('page-title', 'Personel Prim & Hak Ediş Raporu')
@section('page-subtitle', 'Personellerinizin performansını ve ciro bazlı prim hak edişlerini inceleyin.')

@section('content')
<div class="space-y-6">

    {{-- Başlık ve Filtre Barı --}}
    <div class="glass-card rounded-2xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <span>💰</span> Prim & Hak Ediş Raporu
            </h2>
            <p class="text-xs text-[#7070a0] mt-1">İşletmenizde gerçekleşen randevulardan üretilen net prim hak edişleri.</p>
        </div>

        {{-- Dönem Filtresi --}}
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.staff.commissions', ['period' => 'this_month']) }}" class="btn btn-sm {{ $period === 'this_month' ? 'btn-primary' : 'btn-secondary' }} text-xs">
                Bu Ay
            </a>
            <a href="{{ route('admin.staff.commissions', ['period' => 'last_month']) }}" class="btn btn-sm {{ $period === 'last_month' ? 'btn-primary' : 'btn-secondary' }} text-xs">
                Geçen Ay
            </a>
            <a href="{{ route('admin.staff.commissions', ['period' => 'this_year']) }}" class="btn btn-sm {{ $period === 'this_year' ? 'btn-primary' : 'btn-secondary' }} text-xs">
                Bu Yıl
            </a>
            <a href="{{ route('admin.staff.commissions', ['period' => 'all']) }}" class="btn btn-sm {{ $period === 'all' ? 'btn-primary' : 'btn-secondary' }} text-xs">
                Tüm Zamanlar
            </a>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary btn-sm text-xs ml-2">
                ← Personel Listesi
            </a>
        </div>
    </div>

    {{-- Özet KPI Kartları --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-purple-500/20">
            <p class="text-xs font-semibold text-[#7070a0] uppercase">ÜRETİLEN TOPLAM CİRO</p>
            <p class="text-2xl font-black gradient-text mt-2">{{ number_format($grandTotalRevenue, 0, ',', '.') }} ₺</p>
            <p class="text-[10px] text-[#7070a0] mt-1">Tamamlanan randevulardan elde edilen tutar</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-green-500/20 bg-gradient-to-br from-green-500/5 to-emerald-500/5">
            <p class="text-xs font-semibold text-[#7070a0] uppercase">TOPLAM PERSONEL HAK EDİŞİ</p>
            <p class="text-2xl font-black text-green-400 mt-2">{{ number_format($grandTotalCommission, 0, ',', '.') }} ₺</p>
            <p class="text-[10px] text-green-400/70 mt-1">Dağıtılacak toplam prim tutarı</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-blue-500/20">
            <p class="text-xs font-semibold text-[#7070a0] uppercase">TAMAMLANAN RANDEVU</p>
            <p class="text-2xl font-black text-white mt-2">{{ $grandTotalAppts }} Adet</p>
            <p class="text-[10px] text-[#7070a0] mt-1">Seçilen dönemdeki hizmet sayısı</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-yellow-500/20 bg-gradient-to-br from-yellow-500/5 to-amber-500/5">
            <p class="text-xs font-semibold text-[#7070a0] uppercase">CİRO LİDERİ PERSONEL</p>
            <p class="text-lg font-bold text-yellow-300 mt-2 flex items-center gap-1.5 truncate">
                <span>🏆</span> {{ $topPerformer }}
            </p>
            <p class="text-[10px] text-yellow-400/70 mt-1">En yüksek ciro kazandıran çalışan</p>
        </div>
    </div>

    {{-- Personel Performans & Prim Tablosu --}}
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <span>📊</span> Personel Performans ve Prim Dağılım Tablosu
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-white/10 text-xs text-[#7070a0] uppercase">
                        <th class="py-3 px-4">Personel</th>
                        <th class="py-3 px-4 text-center">Tamamlanan Randevu</th>
                        <th class="py-3 px-4 text-right">Kazandırılan Ciro</th>
                        <th class="py-3 px-4 text-center">Prim Oranı</th>
                        <th class="py-3 px-4 text-right">Net Hak Ediş</th>
                        <th class="py-3 px-4 text-left">En Çok Sattığı Hizmet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($reportData as $row)
                    <tr class="hover:bg-white/3 transition-colors">
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl gradient-brand flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr($row['staff']->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-white text-sm">{{ $row['staff']->name }}</p>
                                    <p class="text-xs text-[#7070a0]">{{ $row['staff']->title ?? 'Personel' }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="py-3.5 px-4 text-center font-semibold text-white">
                            {{ $row['completed_count'] }} Randevu
                        </td>

                        <td class="py-3.5 px-4 text-right font-bold text-white">
                            {{ number_format($row['total_revenue'], 0, ',', '.') }} ₺
                        </td>

                        <td class="py-3.5 px-4 text-center">
                            <span class="badge badge-purple text-xs font-semibold">
                                %{{ number_format($row['commission_rate'], 0) }}
                            </span>
                        </td>

                        <td class="py-3.5 px-4 text-right font-black text-green-400 text-base">
                            {{ number_format($row['earned_commission'], 0, ',', '.') }} ₺
                        </td>

                        <td class="py-3.5 px-4">
                            <span class="badge badge-gray text-xs">
                                ✂️ {{ $row['top_service'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-[#7070a0]">
                            Seçilen dönemde henüz tamamlanan randevu kaydı bulunamadı.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
