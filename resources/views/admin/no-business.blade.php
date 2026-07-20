@extends('layouts.admin')
@section('title','İşletme Bulunamadı')

@section('content')
<div class="max-w-md mx-auto text-center py-12 animate-fade-in-up">
    <div class="w-16 h-16 rounded-2xl bg-orange-500/15 flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <h2 class="text-xl font-bold text-white mb-2">İşletme Profiliniz Bulunamadı</h2>
    <p class="text-sm text-[#7070a0] mb-6">Hesabınızla ilişkilendirilmiş herhangi bir salon veya dükkan bulunamadı. Lütfen sistem yöneticisiyle iletişime geçin.</p>
    
    <div class="flex justify-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary">Çıkış Yap</button>
        </form>
    </div>
</div>
@endsection
