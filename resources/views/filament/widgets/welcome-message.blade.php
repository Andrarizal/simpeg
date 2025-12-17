<x-filament-widgets::widget>
    <x-filament::section class="bg-linear-to-br from-emerald-500 to-teal-600 dark:from-emerald-900 dark:to-teal-950 border-0 ring-0 shadow-lg">
        <div class="flex flex-col md:flex-row items-center gap-6">
            {{-- Avatar --}}
            <div class="shrink-0 relative">
                @if ($avatarUrl = $user->getFilamentAvatarUrl())
                    <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" 
                         class="w-24 h-24 rounded-full object-cover border-4 border-white/20 shadow-md">
                @else
                    <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center text-3xl font-bold text-white border-4 border-white/10">
                        {{ strtoupper($initials) }}
                    </div>
                @endif
            </div>

            {{-- Info User --}}
            <div class="text-center md:text-left flex-1 space-y-1">
                <div class="inline-block px-2 py-0.5 rounded bg-white/20 text-white text-xs font-mono mb-1">
                    {{ $staff->nip ?? 'No NIP' }}
                </div>
                <h2 class="text-2xl font-bold text-white tracking-tight">
                    Selamat Datang, {{ $user->name }}
                </h2>
                <div class="text-emerald-100 text-sm space-y-0.5">
                    <p>{{ $staff->chair->name ?? '-' }} &bull; {{ $staff->unit->name ?? '-' }}</p>
                </div>
            </div>

            {{-- Statistik Mini --}}
            <div class="flex gap-3 w-full md:w-auto">
                <div class="flex-1 md:w-32 bg-white/10 backdrop-blur-sm rounded-xl p-3 text-center border border-white/10">
                    <p class="text-xs text-emerald-100 uppercase tracking-wider">Masa Kerja</p>
                    <p class="text-2xl font-bold text-white">{{ $masaKerja }} <span class="text-xs font-normal">Thn</span></p>
                </div>
                <div class="flex-1 md:w-32 bg-white/10 backdrop-blur-sm rounded-xl p-3 text-center border border-white/10">
                    <p class="text-xs text-emerald-100 uppercase tracking-wider">Pensiun</p>
                    <p class="text-2xl font-bold text-white">{{ $countdownPensiun }} <span class="text-xs font-normal">Thn Lagi</span></p>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>