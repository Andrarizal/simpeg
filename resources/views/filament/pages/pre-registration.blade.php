<div class="flex w-full px-6 lg:px-18">
    <form wire:submit="preRegist" class="space-y-4 w-full self-center">
        <div class="flex justify-center lg:justify-start items-center gap-4 pb-4">
            <img src="{{ asset('img/rsumpyk.png') }}" class="w-14 h-14">
            <h1 class="font-semibold leading-tight lg:text-xl hidden lg:block">Sistem Informasi Manajemen dan <br>Tenaga Pegawai</h1>
            <h1 class="font-semibold leading-tight text-2xl lg:hidden">SIMANTAP</h1>
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