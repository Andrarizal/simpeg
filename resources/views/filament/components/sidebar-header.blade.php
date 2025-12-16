<div 
    class="flex items-center h-16 transition-all duration-300"
    :class="$store.sidebar.isOpen ? 'justify-between' : 'justify-center'"
>
    <a 
        href="/" 
        class="flex items-center gap-3 transition-opacity duration-300"
        x-show="$store.sidebar.isOpen"
        x-transition:enter="delay-100"
    >
        <img src="{{ asset('img/rsumpyk.png') }}" alt="Logo" class="w-8 h-8 rounded-xl shadow-sm">
        
        <div class="flex flex-col">
            <span class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                {{ config('app.name') }}
            </span>
            </div>
    </a>

    <button 
        x-on:click="$store.sidebar.isOpen = !$store.sidebar.isOpen"
        type="button"
        class="flex items-center justify-center w-10 h-10 transition focus:outline-none focus:ring-2 focus:ring-primary-600 rounded-xl group"
        {{-- 
            Logika Styling Tombol:
            - Saat OPEN: Ukuran kecil, abu-abu (default toggle).
            - Saat CLOSED: Ukuran besar, transparan (karena isinya jadi Logo).
        --}}
        :class="$store.sidebar.isOpen 
            ? 'text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white' 
            : 'bg-transparent'"
    >
        <x-heroicon-o-chevron-double-left 
            class="w-5 h-5" 
            x-show="$store.sidebar.isOpen" 
        />

        <div 
            x-show="!$store.sidebar.isOpen" 
            style="display: none;" 
            class="relative flex items-center justify-center w-10 h-6"
        >
            <img 
                src="{{ asset('img/rsumpyk.png') }}" 
                alt="Logo" 
                class="h-8 rounded-xl shadow-sm transition-all duration-300 group-hover:opacity-40 group-hover:scale-90"
            >

            <div 
                class="absolute inset-0 flex items-center justify-center opacity-0 transition-all duration-300 scale-50 group-hover:scale-100 group-hover:opacity-100"
            >
                <div class="flex items-center justify-center w-12 h-10 bg-white/80 backdrop-blur-sm rounded-xl shadow-md dark:bg-gray-900/80 ring-1 ring-gray-900/5 dark:ring-white/10">
                    <x-heroicon-o-chevron-double-right class="w-5 h-5 text-primary-600 font-bold" />
                </div>
            </div>
        </div>
    </button>
</div>