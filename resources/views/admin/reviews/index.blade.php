@extends('layouts.admin')
@section('title','Yorumlar')
@section('page-title','Yorumlar')
@section('page-subtitle','Müşteri değerlendirmeleri')
@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-1">
            @for($i=1; $i<=5; $i++)
            <svg class="w-5 h-5 {{ $i<=round($avgRating) ? 'text-yellow-400':'text-[#32324a]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            @endfor
        </div>
        <span class="text-xl font-bold text-white">{{ number_format($avgRating,1) }}</span>
        <span class="text-sm text-[#7070a0]">Ortalama Puan</span>
    </div>
    <form method="GET" class="flex gap-2 flex-wrap">
        <select name="rating" class="form-input w-auto text-sm" onchange="this.form.submit()">
            <option value="">Tüm Puanlar</option>
            @foreach([5,4,3,2,1] as $r)<option value="{{ $r }}" {{ request('rating')==$r ? 'selected':'' }}>{{ $r }} ⭐</option>@endforeach
        </select>
        <select name="replied" class="form-input w-auto text-sm" onchange="this.form.submit()">
            <option value="">Tümü</option>
            <option value="0" {{ request('replied')==='0' ? 'selected':'' }}>Yanıtlanmamış</option>
            <option value="1" {{ request('replied')==='1' ? 'selected':'' }}>Yanıtlanmış</option>
        </select>
    </form>
</div>

<div class="space-y-3">
    @forelse($reviews as $review)
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-start gap-4 flex-wrap">
            <div class="w-10 h-10 rounded-xl gradient-brand flex items-center justify-center text-white font-bold flex-shrink-0">
                {{ substr($review->customer->name,0,1) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <p class="text-sm font-semibold text-white">{{ $review->customer->name }}</p>
                    <div class="flex gap-0.5">
                        @for($i=1; $i<=5; $i++)<svg class="w-3.5 h-3.5 {{ $i<=$review->rating ? 'text-yellow-400':'text-[#32324a]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
                    </div>
                    <span class="text-xs text-[#7070a0]">{{ $review->created_at->diffForHumans() }}</span>
                    @if($review->staff)<span class="badge badge-gray text-xs">{{ $review->staff->name }}</span>@endif
                </div>
                @if($review->comment)
                <p class="text-sm text-[#d0d0e0]">{{ $review->comment }}</p>
                @endif

                {{-- İşletme Yanıtı --}}
                @if($review->business_reply)
                <div class="mt-3 pl-3 border-l-2 border-purple-500/40">
                    <p class="text-xs text-[#7070a0] mb-1">İşletme Yanıtı • {{ $review->business_replied_at?->format('d M Y') }}</p>
                    <p class="text-sm text-[#d0d0e0]">{{ $review->business_reply }}</p>
                </div>
                @else
                <div x-data="{ open: false }" class="mt-3">
                    <button @click="open=!open" class="text-xs text-[#e879f9] hover:underline">Yanıtla →</button>
                    <form x-show="open" x-transition method="POST" action="{{ route('admin.reviews.reply',$review) }}" class="mt-2 space-y-2">
                        @csrf
                        <textarea name="reply" rows="2" class="form-input text-sm" placeholder="Yanıtınızı yazın..." required></textarea>
                        <button type="submit" class="btn btn-primary btn-sm">Gönder</button>
                    </form>
                </div>
                @endif
            </div>

            <div class="flex gap-2">
                <form method="POST" action="{{ route('admin.reviews.visibility',$review) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn {{ $review->is_visible ? 'btn-secondary':'btn-danger' }} btn-sm" title="{{ $review->is_visible ? 'Gizle':'Göster' }}">
                        @if($review->is_visible)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-16 text-[#7070a0]">Yorum bulunamadı.</div>
    @endforelse
</div>
<div class="mt-4">{{ $reviews->withQueryString()->links() }}</div>
@endsection
