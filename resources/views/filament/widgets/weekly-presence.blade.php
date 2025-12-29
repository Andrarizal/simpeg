<x-filament-widgets::widget>
    <x-filament::section class="p-0">
        {{-- Header Widget (Opsional) --}}
        <div class="px-2 pb-3">
            <h2 class="text-base font-bold text-gray-600 dark:text-gray-300">
                Presensi Minggu Ini
            </h2>
        </div>

        {{-- Container Scrollable untuk Mobile --}}
        <div 
            x-data 
            x-init="
                $nextTick(() => {
                    $el.querySelector('.js-today-marker')?.scrollIntoView({ 
                        behavior: 'smooth', 
                        inline: 'center',
                        block: 'nearest'
                    });
                })
            "
            class="overflow-x-auto w-full">
            {{-- Grid 7 Kolom (Min-width agar tidak gepeng di HP) --}}
            <div class="flex justify-evenly w-full" style="-ms-overflow-style: none; scrollbar-width: none;">
                
                @foreach($days as $day)
                    <div class="flex flex-col items-center justify-center min-w-28 p-4 leading-none rounded-xl text-center group transition hover:bg-gray-50 dark:hover:bg-white/5 
                        {{ $day['is_today'] ? 'bg-primary-50/50 dark:bg-primary-900/10 ring-inset ring ring-primary-500/20' : '' }}
                        {{ $day['is_today'] ? 'js-today-marker' : '' }}">
                        
                        <div class="text-xl font-black text-gray-700 dark:text-gray-200 {{ $day['is_today'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $day['date_idx'] }}
                        </div>

                        {{-- Baris 2: Nama Hari --}}
                        <div class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">
                            @if($day['is_today'])
                            <span class="block text-[9px] text-primary-600 font-bold">(Hari Ini)</span>
                            @else
                            {{ $day['day_name'] }}
                            @endif
                        </div>

                        {{-- Baris 3: Icon Status --}}
                        <div class="mt-4 mb-2 {{ $day['color'] }}">
                            <x-filament::icon
                                :icon="$day['icon']"
                                class="h-8 w-8"
                            />
                        </div>

                        {{-- Baris 4: Label Status --}}
                        <div class="text-[10px] font-semibold {{ $day['color'] }}">
                            {{ $day['label'] }}
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>