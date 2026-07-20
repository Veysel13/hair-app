@extends('layouts.customer')
@section('title', 'Randevularım')

@section('content')
<div class="space-y-6">

    {{-- Yaklaşan Randevular --}}
    <div>
        <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-1.5">
            ⏳ <span>Yaklaşan Randevularım</span>
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($upcomingAppointments as $appt)
            <div class="glass-card rounded-2xl p-4 border-l-4 border-purple-500">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h4 class="text-sm font-bold text-white">{{ $appt->business->name }}</h4>
                        <p class="text-xs text-[#a0a0c0] mt-0.5">{{ $appt->appointment_date->format('d M Y') }} • {{ substr($appt->start_time, 0, 5) }}</p>
                        <p class="text-[10px] text-[#7070a0] mt-1">👤 Uzman: {{ $appt->staff->name }}</p>
                        
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($appt->services as $s)
                            <span class="badge badge-gray text-[9px]">{{ $s->service->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="text-right flex flex-col items-end gap-2">
                        <p class="text-sm font-bold gradient-text">{{ number_format($appt->total_price, 0, ',', '.') }} ₺</p>
                        <span class="badge {{ $appt->status === 'confirmed' ? 'badge-info' : 'badge-warning' }} text-[9px]">
                            {{ $appt->status === 'confirmed' ? 'Onaylı' : 'Bekliyor' }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="glass-card rounded-2xl p-6 text-center text-xs text-[#7070a0] md:col-span-2">
                Yaklaşan randevunuz bulunmamaktadır.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Geçmiş Randevular --}}
    <div>
        <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-1.5">
            ✅ <span>Geçmiş Randevularım</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($pastAppointments as $appt)
            <div class="glass-card rounded-2xl p-4 border-l-4 border-white/5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h4 class="text-sm font-bold text-white">{{ $appt->business->name }}</h4>
                        <p class="text-xs text-[#a0a0c0] mt-0.5">{{ $appt->appointment_date->format('d M Y') }} • {{ substr($appt->start_time, 0, 5) }}</p>
                        <p class="text-[10px] text-[#7070a0] mt-1">👤 Uzman: {{ $appt->staff->name }}</p>

                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($appt->services as $s)
                            <span class="badge badge-gray text-[9px]">{{ $s->service->name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="text-right flex flex-col items-end gap-2">
                        <p class="text-sm font-bold text-white">{{ number_format($appt->total_price, 0, ',', '.') }} ₺</p>
                        @php
                            $badgeClass = match($appt->status) {
                                'completed' => 'badge-success',
                                'cancelled' => 'badge-danger',
                                'no_show'   => 'badge-gray',
                                default     => 'badge-gray',
                            };
                            $statusLabel = match($appt->status) {
                                'completed' => 'Tamamlandı',
                                'cancelled' => 'İptal Edildi',
                                'no_show'   => 'Gelmedi',
                                default     => $appt->status,
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} text-[9px]">{{ $statusLabel }}</span>
                        
                        @if($appt->status === 'completed')
                        <a href="{{ route('book.show', $appt->business) }}?staff_id={{ $appt->staff_id }}&{{ http_build_query(['service_ids' => $appt->services->pluck('service_id')->toArray()]) }}" 
                           class="mt-1 text-[10px] text-[#e879f9] hover:underline font-extrabold flex items-center gap-0.5 transition-all hover:scale-105">
                            🔁 Hızlı Tekrarla
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Rezervasyon Değerlendirme (Review) Alanı --}}
                @if($appt->review)
                    <div class="mt-3 pt-3 border-t border-white/5 space-y-2">
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="text-[#7070a0] font-semibold">Değerlendirmeniz:</span>
                            <div class="flex items-center gap-2">
                                <span class="text-[#a0a0c0]">Salon: <span class="text-yellow-400 font-bold">★{{ $appt->review->rating }}</span></span>
                                <span class="text-[#a0a0c0]">Personel: <span class="text-yellow-400 font-bold">★{{ $appt->review->staff_rating }}</span></span>
                            </div>
                        </div>
                        @if($appt->review->comment)
                        <p class="text-xs text-[#a0a0c0] italic bg-white/3 p-2 rounded-xl mt-1 leading-relaxed">"{{ $appt->review->comment }}"</p>
                        @endif
                        @if($appt->review->business_reply)
                        <div class="bg-purple-950/20 border border-purple-500/10 p-3 rounded-xl mt-1.5 space-y-1">
                            <p class="text-[10px] font-bold text-[#e879f9] flex items-center gap-1">
                                <span>💬</span> Salon Yanıtı:
                            </p>
                            <p class="text-xs text-[#a0a0c0] leading-relaxed">{{ $appt->review->business_reply }}</p>
                        </div>
                        @endif
                    </div>
                @elseif($appt->status === 'completed')
                    <div x-data="{ showForm: false, salonRating: 5, staffRating: 5 }" class="mt-3 pt-3 border-t border-white/5">
                        <button type="button" @click="showForm = !showForm" class="text-[11px] text-[#e879f9] hover:underline font-bold flex items-center gap-1 transition-colors">
                            <span x-text="showForm ? '✕ İptal' : '⭐ Randevuyu Değerlendir'"></span>
                        </button>

                        <form x-show="showForm" x-transition method="POST" action="{{ route('customer.reviews.store', $appt) }}" class="mt-3 bg-white/3 p-4 rounded-xl border border-white/5 space-y-3">
                            @csrf
                            
                            <!-- Salon Puanı -->
                            <div class="flex items-center justify-between">
                                <span class="form-label text-[10px]">Salon Hizmet Puanı</span>
                                <div class="flex gap-1">
                                    <template x-for="i in 5">
                                        <button type="button" @click="salonRating = i" class="text-base transition-transform hover:scale-115 focus:outline-none">
                                            <span :class="i <= salonRating ? 'text-yellow-400' : 'text-[#32324a]'">★</span>
                                        </button>
                                    </template>
                                </div>
                                <input type="hidden" name="rating" :value="salonRating">
                            </div>

                            <!-- Uzman Personel Puanı -->
                            <div class="flex items-center justify-between">
                                <span class="form-label text-[10px]">Personel Puanı ({{ $appt->staff->name }})</span>
                                <div class="flex gap-1">
                                    <template x-for="i in 5">
                                        <button type="button" @click="staffRating = i" class="text-base transition-transform hover:scale-115 focus:outline-none">
                                            <span :class="i <= staffRating ? 'text-yellow-400' : 'text-[#32324a]'">★</span>
                                        </button>
                                    </template>
                                </div>
                                <input type="hidden" name="staff_rating" :value="staffRating">
                            </div>

                            <!-- Yorum Metni -->
                            <div>
                                <label class="form-label text-[10px]">Yorum ve Görüşleriniz (İsteğe Bağlı)</label>
                                <textarea name="comment" rows="2" class="form-input text-xs" placeholder="Deneyiminizi kısaca yazın..."></textarea>
                            </div>

                            <div class="flex justify-end pt-1">
                                <button type="submit" class="btn btn-primary btn-sm py-1.5 px-4 text-xs font-bold shadow-md shadow-purple-500/10">
                                    Gönder 🚀
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
            @empty
            <div class="glass-card rounded-2xl p-6 text-center text-xs text-[#7070a0] md:col-span-2">
                Geçmiş randevunuz bulunmamaktadır.
            </div>
            @endforelse
        </div>
        
        <div class="mt-4">
            {{ $pastAppointments->links() }}
        </div>
    </div>

</div>
@endsection
