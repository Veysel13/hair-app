<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gün Sonu Z-Raporu — {{ $business->name }} ({{ $reportDate->format('d.m.Y') }})</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; color: #000000 !important; font-size: 11pt; }
            .print-container { max-width: 100% !important; margin: 0 !important; padding: 0 !important; shadow: none !important; }
            .print-card { border: 1px solid #ddd !important; background: #fff !important; color: #000 !important; box-shadow: none !important; }
            .text-white { color: #000 !important; }
            .text-[#a0a0c0], .text-[#7070a0], .text-[#d0d0e0] { color: #444 !important; }
            .border-white\/5, .border-white\/10 { border-color: #ddd !important; }
            .bg-white\/3, .bg-white\/5 { background: #f9f9f9 !important; }
            .badge { border: 1px solid #ccc !important; color: #000 !important; background: #eee !important; }
            .print-break { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-[#0f0f14] text-[#f1f1f8] min-h-screen p-4 md:p-8 font-sans">

    {{-- Üst Eylem Barı (Yazdırırken Gizlenir) --}}
    <div class="max-w-4xl mx-auto mb-6 no-print flex items-center justify-between gap-4 flex-wrap bg-[#161622] p-4 rounded-2xl border border-white/10 shadow-xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.finance.index') }}" class="btn btn-secondary text-xs font-semibold">
                ← Finans Paneline Dön
            </a>
            <form method="GET" action="{{ route('admin.finance.z-report') }}" class="flex items-center gap-2">
                <label class="text-xs text-[#a0a0c0] font-medium">Tarih Seç:</label>
                <input type="date" name="date" value="{{ $reportDate->format('Y-m-d') }}" onchange="this.form.submit()" class="form-input text-xs py-1 px-2.5">
            </form>
        </div>

        <button onclick="window.print()" class="btn btn-primary text-xs font-bold flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg shadow-purple-500/20">
            <span>🖨️</span> Z-Raporunu Yazdır / PDF İndir
        </button>
    </div>

    {{-- Yazdırılabilir A4 Rapor Kabuğu --}}
    <div class="max-w-4xl mx-auto glass-card rounded-3xl p-8 border border-purple-500/20 print-container space-y-6 bg-[#161622]">

        {{-- 1. Başlık & Salon Künyesi --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-6 print-break">
            <div class="flex items-center gap-4">
                @if($business->logo_url)
                <img src="{{ asset('storage/' . $business->logo_url) }}" class="w-16 h-16 rounded-2xl object-cover border border-white/10">
                @else
                <div class="w-16 h-16 rounded-2xl gradient-brand flex items-center justify-center text-white text-2xl font-bold">
                    {{ substr($business->name, 0, 1) }}
                </div>
                @endif
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $business->name }}</h1>
                    <p class="text-xs text-[#a0a0c0]">{{ $business->address ?? 'Kuaför & Güzellik Salonu' }}</p>
                    <p class="text-xs text-[#7070a0]">Tel: {{ $business->phone ?? '—' }}</p>
                </div>
            </div>

            <div class="text-left sm:text-right border-l sm:border-l-0 sm:border-r border-white/10 pl-4 sm:pl-0 sm:pr-4">
                <span class="badge badge-purple text-xs font-mono font-bold mb-1 block w-fit sm:ml-auto">GÜN SONU Z-RAPORU</span>
                <p class="text-xs font-mono text-white font-bold">Rapor No: {{ $zReportNo }}</p>
                <p class="text-xs text-[#a0a0c0]">Kapanış Tarihi: <strong class="text-white">{{ $reportDate->translatedFormat('d F Y, l') }}</strong></p>
                <p class="text-[10px] text-[#7070a0]">Basım Zamanı: {{ now()->format('d.m.Y H:i:s') }}</p>
            </div>
        </div>

        {{-- 2. Kasa Genel Özet Kartları --}}
        <div class="print-break space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-purple-400">1. Kasa Özet Durumu (Gün Sonu Bakiye)</h3>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="bg-white/3 p-3.5 rounded-2xl border border-white/5 print-card">
                    <p class="text-[10px] text-[#7070a0] font-bold uppercase">💵 Nakit Kasa</p>
                    <p class="text-base font-bold text-green-400 mt-1">{{ number_format($cashTotal, 2, ',', '.') }} ₺</p>
                </div>

                <div class="bg-white/3 p-3.5 rounded-2xl border border-white/5 print-card">
                    <p class="text-[10px] text-[#7070a0] font-bold uppercase">💳 Kart Kasa</p>
                    <p class="text-base font-bold text-purple-400 mt-1">{{ number_format($cardTotal, 2, ',', '.') }} ₺</p>
                </div>

                <div class="bg-white/3 p-3.5 rounded-2xl border border-white/5 print-card">
                    <p class="text-[10px] text-[#7070a0] font-bold uppercase">🎁 Toplanan Bahşiş</p>
                    <p class="text-base font-bold text-yellow-400 mt-1">{{ number_format($tipTotal, 2, ',', '.') }} ₺</p>
                </div>

                <div class="bg-white/3 p-3.5 rounded-2xl border border-white/5 print-card">
                    <p class="text-[10px] text-[#7070a0] font-bold uppercase">📉 Günlük Gider</p>
                    <p class="text-base font-bold text-red-400 mt-1">{{ number_format($totalExpense, 2, ',', '.') }} ₺</p>
                </div>

                <div class="col-span-2 sm:col-span-1 bg-green-950/40 p-3.5 rounded-2xl border border-green-500/40 print-card">
                    <p class="text-[10px] text-green-300 font-bold uppercase">💰 Net Kasa Bakiye</p>
                    <p class="text-base font-bold text-green-300 mt-1">{{ number_format($netCashBalance, 2, ',', '.') }} ₺</p>
                </div>
            </div>
        </div>

        {{-- 3. Personel Bazlı Ciro & Prim Dağılım Tablosu --}}
        <div class="print-break space-y-3 pt-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-purple-400">2. Personel Günlük Ciro ve Prim Hak Ediş Dağılımı</h3>
            <div class="overflow-x-auto rounded-2xl border border-white/10">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-white/5 text-[#a0a0c0] border-b border-white/10">
                            <th class="p-3">Personel</th>
                            <th class="p-3">Unvan</th>
                            <th class="p-3 text-center">Randevu Adedi</th>
                            <th class="p-3 text-right">Kazandırılan Ciro</th>
                            <th class="p-3 text-right">Prim Oranı (%)</th>
                            <th class="p-3 text-right">Net Hak Ediş (₺)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($staffPerformance as $row)
                        <tr>
                            <td class="p-3 font-semibold text-white">{{ $row['staff']->name }}</td>
                            <td class="p-3 text-[#a0a0c0]">{{ $row['staff']->title }}</td>
                            <td class="p-3 text-center font-bold text-white">{{ $row['count'] }}</td>
                            <td class="p-3 text-right font-bold text-green-400">{{ number_format($row['revenue'], 2, ',', '.') }} ₺</td>
                            <td class="p-3 text-right text-purple-300">%{{ number_format($row['rate'], 0) }}</td>
                            <td class="p-3 text-right font-bold text-purple-400">{{ number_format($row['commission'], 2, ',', '.') }} ₺</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-[#7070a0]">Bugün için tamamlanmış personel randevusu bulunamadı.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 4. Günün Adisyon Listesi (Tamamlanan Randevular) --}}
        <div class="print-break space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-purple-400">3. Günün Kapanan Adisyonları (Randevu Dökümü)</h3>
                <span class="text-xs text-[#a0a0c0]">Toplam {{ $completedAppts->count() }} Adisyon</span>
            </div>
            <div class="overflow-x-auto rounded-2xl border border-white/10">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-white/5 text-[#a0a0c0] border-b border-white/10">
                            <th class="p-3">Saat</th>
                            <th class="p-3">Müşteri</th>
                            <th class="p-3">Personel</th>
                            <th class="p-3">Ödeme Türü</th>
                            <th class="p-3 text-right">Tutar (₺)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($completedAppts as $appt)
                        @php
                        $methodBadge = match($appt->payment_method) {
                            'cash' => '💵 Nakit',
                            'card' => '💳 Kredi Kartı',
                            'split' => '🔀 Parçalı (' . number_format($appt->cash_amount, 0) . '₺ N / ' . number_format($appt->card_amount, 0) . '₺ K)',
                            default => '💳 Online'
                        };
                        @endphp
                        <tr>
                            <td class="p-3 font-mono text-[#a0a0c0]">{{ substr($appt->start_time, 0, 5) }}</td>
                            <td class="p-3 font-semibold text-white">{{ $appt->customer->name }}</td>
                            <td class="p-3 text-[#a0a0c0]">{{ $appt->staff->name }}</td>
                            <td class="p-3 font-medium text-white">{{ $methodBadge }}</td>
                            <td class="p-3 text-right font-bold text-green-400">{{ number_format($appt->total_price, 2, ',', '.') }} ₺</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-[#7070a0]">Bugün kapanan adisyon bulunmamaktadır.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 5. Günün Gider Listesi --}}
        @if($expenses->count() > 0)
        <div class="print-break space-y-3 pt-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-purple-400">4. Günlük Gider Kayıtları</h3>
            <div class="overflow-x-auto rounded-2xl border border-white/10">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-white/5 text-[#a0a0c0] border-b border-white/10">
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Açıklama</th>
                            <th class="p-3 text-right">Tutar (₺)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($expenses as $e)
                        <tr>
                            <td class="p-3 font-medium text-white">{{ $e->category_label }}</td>
                            <td class="p-3 text-[#a0a0c0]">{{ $e->description }}</td>
                            <td class="p-3 text-right font-bold text-red-400">{{ number_format($e->amount, 2, ',', '.') }} ₺</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- 6. Kapanış Onayı & İmza Alanı --}}
        <div class="print-break pt-8 border-t border-white/10 grid grid-cols-2 gap-8 text-center text-xs">
            <div class="space-y-12">
                <p class="font-bold text-white">Kasa Sorumlusu İmzası</p>
                <div class="border-b border-dashed border-white/20 w-3/4 mx-auto"></div>
                <p class="text-[10px] text-[#7070a0]">Ad Soyad & İmza</p>
            </div>
            <div class="space-y-12">
                <p class="font-bold text-white">Salon Sahibi / Yetkili İmzası</p>
                <div class="border-b border-dashed border-white/20 w-3/4 mx-auto"></div>
                <p class="text-[10px] text-[#7070a0]">Kasa Kapanış Onayı</p>
            </div>
        </div>

    </div>
</body>
</html>
