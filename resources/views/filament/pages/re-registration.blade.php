<div class="flex w-full">
    <form wire:submit="preRegist" class="space-y-4 w-full self-center">
        {{ $this->form }}
        {{-- <div class="mt-4 space-y-4 w-full lg:w-[calc(33%-12px)]">
            <x-filament::button type="submit" class="w-full">
                Registrasi Ulang
            </x-filament::button>
        </div> --}}
    </form>
</div>