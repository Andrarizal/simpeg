@php
    $score = $this->averageScore; 
    $colorClass = match (true) {
        $score >= 80 => 'bg-info-200/10 text-info-200 ring-info-600/20',
        $score >= 70 => 'bg-emerald-200/10 text-emerald-200 ring-emerald-600/20',
        $score >= 50 => 'bg-amber-200/10 text-amber-200 ring-amber-600/20',
        default      => 'bg-red-200/10 text-red-200 ring-red-600/20',
    };
@endphp

<div class="flex flex-col md:flex-row items-center justify-between p-2 border-t dark:border-white/10">
    
    {{-- BAGIAN KIRI: Info Rata-rata --}}
    <div class="w-full md:w-auto mb-2 md:mb-0 px-2">
        {{-- Hanya tampilkan jika sedang di tab Penilaian --}}
        <div class="text-sm text-gray-500 dark:text-gray-400 font-medium flex items-center">
            Rata-rata Periode: 
            <span class="{{ $colorClass }} ml-2 px-2 py-1 rounded-md text-xs font-bold ring-1 ring-inset">
                {{ number_format($score, 2) }}
            </span>
        </div>
    </div>
</div>