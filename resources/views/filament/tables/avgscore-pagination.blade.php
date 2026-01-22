    @php
        $score = $this->averageScore; 
        $colorClass = match (true) {
            $score >= 80 => 'bg-info-300/10 text-info-700 ring-info-700/20 dark:bg-info-300/10 dark:text-info-300 dark:ring-info-300/20',
            $score >= 70 => 'bg-emerald-300/10 text-emerald-700 ring-emerald-700/20 dark:bg-emerald-300/10 dark:text-emerald-300 dark:ring-emerald-300/20',
            $score >= 50 => 'bg-amber-300/10 text-amber-700 ring-amber-700/20 dark:bg-amber-300/10 dark:text-amber-300 dark:ring-amber-300/20',
            default      => 'bg-red-300/10 text-red-700 ring-red-700/20 dark:bg-red-300/10 dark:text-red-300 dark:ring-red-300/20',
        };
    @endphp

    <div class="flex flex-col md:flex-row items-center justify-between p-2">
        
        <div class="w-full md:w-auto mb-2 md:mb-0 px-4">
            <div class="text-sm text-gray-800 dark:text-gray-200 font-medium flex items-center">
                Rata-rata Periode: 
                <span class="{{ $colorClass }} ml-2 px-2 py-1 rounded-xl text-xs font-bold ring-1 ring-inset">
                    {{ number_format($score, 2) }}
                </span>
            </div>
        </div>
    </div>