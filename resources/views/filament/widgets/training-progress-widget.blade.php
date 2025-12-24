<x-filament-widgets::widget>
    <x-filament::section class="h-full *:h-full *:flex *:items-center *:justify-stretch">
        <style>
            .fi-section .fi-section-content {
                width: 100%
            }
        </style>
    <div class="flex items-center justify-between mb-2 w-full">
        <h2 class="text-sm font-medium text-gray-500">Progres Pelatihan Tahunan</h2>
        <span class="text-sm font-bold {{ $percentage >= 100 ? 'text-success-600' : 'text-primary-600' }}">
            {{ $total_hours }} / {{ $target }} Jam
        </span>
    </div>

    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
        <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-500" 
             style="width: {{ $percentage }}%"></div>
    </div>
    
    <p class="mt-2 text-xs text-gray-400">
        {{ $percentage >= 100 ? '🎉 Target tercapai!' : 'Masih kurang ' . $target - $total_hours . ' jam lagi untuk mencapai target' }}
    </p>
</x-filament::section>
</x-filament-widgets::widget>
