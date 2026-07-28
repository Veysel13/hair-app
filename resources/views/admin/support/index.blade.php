@extends('layouts.admin')
@section('title','Canlı Destek & İletişim')
@section('page-title','Canlı Destek & İletişim Merkezi')
@section('page-subtitle','Salonim ekibiyle iletişim kurun, sorularınızı sorun, görüş ve önerilerinizi paylaşın.')

@section('content')
<div x-data="{ showNewModal: false }" class="space-y-6">

    {{-- Üst Banner --}}
    <div class="glass-card rounded-2xl p-6 bg-gradient-to-r from-purple-950/40 via-[#161622] to-[#12121a] border border-purple-500/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 text-2xl flex-shrink-0">
                💬
            </div>
            <div>
                <h2 class="text-base font-bold text-white">Salonim Canlı Destek & İletişim Merkezi</h2>
                <p class="text-xs text-[#a0a0c0]">Soru, görüş, teknik destek veya önerilerinizi 7/24 iletin. Destek ekibimiz yanıtladığında anlık bildirim alırsınız.</p>
            </div>
        </div>
        <div>
            <button @click="showNewModal = true" class="btn btn-primary text-xs font-bold py-2.5 px-4 flex items-center gap-2 shadow-lg shadow-purple-500/20 whitespace-nowrap">
                <span>➕</span> Yeni Talep / Soru Oluştur
            </button>
        </div>
    </div>

    {{-- Ana Grid (Sol: Talepler Listesi, Sağ: Sohbet Ekranı) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Sol: Talep Listesi --}}
        <div class="glass-card rounded-2xl p-4 space-y-3 h-[600px] flex flex-col">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Destek Konuları</h3>
                <span class="badge badge-purple text-[10px]">{{ $tickets->count() }} Kayıt</span>
            </div>

            <div class="flex-1 overflow-y-auto space-y-2 pr-1">
                @forelse($tickets as $t)
                <a href="{{ route('admin.support.index', ['ticket' => $t->id]) }}" 
                   class="block p-3.5 rounded-xl border transition-all {{ $activeTicket && $activeTicket->id === $t->id ? 'bg-purple-900/30 border-purple-500/50 shadow-md' : 'bg-white/3 border-white/5 hover:bg-white/5' }}">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="badge {{ match($t->status){ 'open'=>'badge-warning', 'replied'=>'badge-success', default=>'badge-gray' } }} text-[9px]">
                            {{ $t->status_label }}
                        </span>
                        <span class="text-[10px] text-[#7070a0]">{{ $t->last_message_at ? $t->last_message_at->diffForHumans() : $t->created_at->diffForHumans() }}</span>
                    </div>

                    <h4 class="text-xs font-bold text-white truncate">{{ $t->subject }}</h4>

                    <div class="flex items-center justify-between gap-2 mt-2 text-[10px] text-[#a0a0c0]">
                        <span>{{ $t->category_label }}</span>
                        @if(Auth::user()->isAdmin())
                        <span class="font-semibold text-purple-300">🏢 {{ $t->business->name }}</span>
                        @endif
                    </div>
                </a>
                @empty
                <div class="text-center py-16 text-[#7070a0] text-xs">
                    Henüz hiç destek talebi veya mesaj bulunmuyor.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Sağ: Canlı Mesajlaşma Ekranı (Chat Window) --}}
        <div class="lg:col-span-2 glass-card rounded-2xl p-5 h-[600px] flex flex-col justify-between">
            @if($activeTicket)

            {{-- Chat Header --}}
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-sm font-bold text-white">{{ $activeTicket->subject }}</h3>
                        <span class="badge {{ match($activeTicket->status){ 'open'=>'badge-warning', 'replied'=>'badge-success', default=>'badge-gray' } }} text-[10px]">
                            {{ $activeTicket->status_label }}
                        </span>
                        <span class="badge badge-purple text-[10px]">{{ $activeTicket->category_label }}</span>
                    </div>
                    <p class="text-[11px] text-[#a0a0c0]">
                        Talep Sahibi: <strong>{{ $activeTicket->user->name }}</strong> ({{ $activeTicket->business->name }}) — {{ $activeTicket->created_at->format('d.m.Y H:i') }}
                    </p>
                </div>
            </div>

            {{-- Chat Baloncukları (Messages) --}}
            <div class="flex-1 overflow-y-auto my-4 space-y-4 pr-2">
                @foreach($activeTicket->messages as $msg)
                @php
                $isAdmin = $msg->is_admin_reply;
                @endphp
                <div class="flex flex-col {{ $isAdmin ? 'items-start' : 'items-end' }}">
                    <div class="flex items-center gap-1.5 mb-1 text-[10px] text-[#7070a0]">
                        <span class="font-bold {{ $isAdmin ? 'text-green-400' : 'text-purple-300' }}">
                            {{ $isAdmin ? '🛡️ Salonim Destek Ekibi' : '👤 ' . $msg->user->name }}
                        </span>
                        <span>• {{ $msg->created_at->format('H:i, d M') }}</span>
                    </div>
                    <div class="max-w-lg p-3.5 rounded-2xl text-xs leading-relaxed shadow-lg {{ $isAdmin ? 'bg-gradient-to-br from-green-950/60 to-emerald-950/40 text-green-100 border border-green-500/30 rounded-tl-none' : 'bg-gradient-to-br from-purple-900/60 to-indigo-900/40 text-purple-100 border border-purple-500/30 rounded-tr-none' }}">
                        {!! nl2br(e($msg->message)) !!}
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Mesaj Gönder Kutusu --}}
            <form method="POST" action="{{ route('admin.support.reply', $activeTicket) }}" class="border-t border-white/10 pt-4 flex gap-3">
                @csrf
                <textarea name="message" rows="2" class="form-input text-xs flex-1" required placeholder="Mesajınızı yazın..."></textarea>
                <button type="submit" class="btn btn-primary text-xs font-bold px-5 flex items-center justify-center gap-1.5 self-end py-3 bg-gradient-to-r from-purple-600 to-indigo-600">
                    <span>⚡</span> Gönder
                </button>
            </form>

            @else
            <div class="h-full flex flex-col items-center justify-center text-center text-[#7070a0] space-y-3">
                <div class="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center text-3xl">
                    💬
                </div>
                <h4 class="text-sm font-bold text-white">Sohbet Seçilmedi</h4>
                <p class="text-xs max-w-xs">Sol taraftaki listeden bir destek konusu seçin veya yukarıdaki butondan yeni talep oluşturun.</p>
            </div>
            @endif
        </div>

    </div>

    {{-- Yeni Talep Oluşturma Modalı --}}
    <div x-show="showNewModal" x-transition.opacity class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.outside="showNewModal = false" class="glass-card rounded-3xl p-6 max-w-lg w-full border border-purple-500/30 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>➕</span> Yeni Destek & İletişim Talebi
                </h3>
                <button @click="showNewModal = false" class="text-[#7070a0] hover:text-white text-lg">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.support.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="form-label">Konu / Başlık *</label>
                    <input type="text" name="subject" class="form-input text-xs" required placeholder="Örn: Prim Oranları Hakkında Soru">
                </div>

                <div>
                    <label class="form-label">Kategori *</label>
                    <select name="category" class="form-input text-xs" required>
                        <option value="question">❓ Soru & Bilgi Talebi</option>
                        <option value="suggestion">💡 Görüş & Öneri</option>
                        <option value="technical">🔧 Teknik Destek</option>
                        <option value="billing">💳 Fatura / Ödeme</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Mesajınız *</label>
                    <textarea name="message" rows="4" class="form-input text-xs" required placeholder="Sorunuzu veya iletmek istediğiniz detayları buraya yazın..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="showNewModal = false" class="btn btn-secondary text-xs">İptal</button>
                    <button type="submit" class="btn btn-primary text-xs font-bold px-5">Talebi Gönder 🚀</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
