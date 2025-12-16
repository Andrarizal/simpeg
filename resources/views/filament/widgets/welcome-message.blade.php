<x-filament-widgets::widget>
    <x-filament::section class="bg-emerald-400 dark:bg-emerald-600">
        {{-- Profil Singkat --}}
        <div class="md:grid md:grid-cols-12 md:gap-6">
            <div class="md:col-span-2 flex justify-center md:justify-end items-center min-w-full">
                @php
                $avatarUrl = $user->getFilamentAvatarUrl();
                $initials = collect(explode(' ', $user->name))
                    ->map(fn ($word) => mb_substr($word, 0, 1))
                    ->join('');
                @endphp
                @if ($avatarUrl)
                    <img 
                        src="{{ $avatarUrl }}" 
                        alt="{{ $user->name }}" 
                        class="object-cover rounded-full w-24 h-24"
                    />
                @else
                    <span class="text-2xl font-semibold text-gray-300 dark:text-gray-700 bg-amber-950 p-6 py-7 rounded-full">
                        {{ substr(strtoupper($initials), 0, 2) }}
                    </span>
                @endif
            </div>
            <div class="my-4 md:col-span-6">
                <p class="text-center md:text-left font-semibold text-gray-600 dark:text-gray-300 text-sm">{{ $user->staff->nip }}</p>
                <h3 class="text-center md:text-left text-2xl font-semibold">{{ $user->name }}</h3>
                <p class="text-center md:text-left text-gray-600 dark:text-gray-300 text-sm">Jabatan: {{ $user->staff->chair->name }}</p>
                <p class="text-center md:text-left text-gray-600 dark:text-gray-300 text-sm">Unit Kerja:    {{ $user->staff->unit->name }}</p>
            </div>
            <div class="md:col-span-4 flex justify-around items-center md:gap-4">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-3xl shadow-xl w-32 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Masa Kerja</p>
                    <p class="text-3xl font-semibold text-gray-800 dark:text-gray-100">
                        {{ $masaKerja }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">tahun</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-3xl shadow-xl w-32 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pensiun</p>
                    <p class="text-3xl font-semibold text-gray-800 dark:text-gray-100">
                        {{ $countdownPensiun }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">tahun lagi</p>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
