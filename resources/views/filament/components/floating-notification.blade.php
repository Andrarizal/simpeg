<div class="floating-notif fixed right-8 top-4 z-10" wire:poll.5s>
    {{-- Container Utama --}}
    <div class="relative flex items-center justify-center bg-white border border-gray-200 shadow-xl rounded-full dark:bg-gray-900 dark:border-white/10" 
         style="width: 40px; height: 40px;">
        
        {{-- 1. Komponen Notifikasi Bawaan Filament (Tombol & Modal) --}}
        {{-- Kita sembunyikan badge bawaan Filament lewat CSS khusus di sini jika double --}}
        <div class="[&_.fi-icon-btn-badge]:hidden flex items-center justify-center w-full h-full">
            @livewire(\Filament\Livewire\DatabaseNotifications::class)
        </div>

        {{-- 2. Custom Badge Merah (Indikator) --}}
        @php
            $unreadCount = \Illuminate\Support\Facades\Auth::user()->unreadNotifications()->count();
        @endphp

        @if($unreadCount > 0)
            <span class="total-notifications absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white shadow-sm ring-2 ring-white pointer-events-none dark:ring-gray-900">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
            
            <span class="absolute -top-1 -right-1 inline-flex h-5 w-5 rounded-full bg-red-400 opacity-75 animate-ping pointer-events-none"></span>
        @endif
    </div>
</div>