<div class="flex w-full px-6 lg:px-18">
    <form wire:submit="preRegist" class="space-y-4 w-full self-center">
        <div class="flex flex-col md:flex-row justify-center lg:justify-start items-center gap-4 pb-4">
            <img src="{{ asset('img/rsumpyk.png') }}" class="w-12 h-12 border border-gray-700/20 dark:border-0 bg-white p-1 rounded-lg block shadow-lg">
            <div class="-mt-2 md:-mt-1">
              <h1 class="font-bold text-center md:text-start text-3xl lg:text-4xl">SIMANTAP</h1>
              <h2 class="font-light text-center md:text-start text-[10px] lg:text-xs md:-mt-1">Sistem Informasi Manajemen dan <br class="md:hidden">Tenaga Pegawai</h2>
            </div>
          </div>
            {{ $this->form }}

            <div class="mt-4 space-y-4">
            <x-filament::button type="submit" class="w-full">
                Pre-Register
            </x-filament::button>
            <div class="text-center lg:text-left">
                <span class="text-sm text-gray-500">Sudah punya akun?</span>
                <a href="/"
                class="text-sm text-primary-600 font-semibold hover:underline">
                Login
                </a>
            </div>
        </div>
    </form>
</div>