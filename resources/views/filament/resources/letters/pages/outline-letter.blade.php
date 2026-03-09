<x-filament-panels::page>
    <form wire:submit="save">
        
        {{ $this->form }}

        <div class="mt-6 flex gap-3">
            {{ $this->submitAction }}
            
            <x-filament::button tag="a" href="{{ \App\Filament\Resources\Letters\LetterResource::getUrl('index') }}" color="gray">
                Batal
            </x-filament::button>
        </div>
        
    </form>
</x-filament-panels::page>