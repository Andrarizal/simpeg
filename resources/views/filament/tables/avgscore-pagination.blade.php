@php
    $score = $this->averageScore; 
    $colorClass = match (true) {
        $score >= 80 => 'bg-info-50 text-info-700 ring-info-600/20',
        $score >= 70 => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        $score >= 50 => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        default      => 'bg-red-50 text-red-700 ring-red-600/20',
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