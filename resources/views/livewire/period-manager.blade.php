<div>
    <div class="overflow-y-auto rounded-xl max-h-[400px] shadow-sm">
        {{ $this->table }}
    </div>
    <div class="py-4 mb-6 {{ $editingId ? 'bg-orange-50 border-orange-200 dark:bg-orange-900/20' : 'bg-gray-50 dark:bg-gray-900 dark:border-gray-700' }}">
        <div class="flex justify-between items-center mb-4">
            <h3 class="mb-0 text-lg font-bold {{ $editingId ? 'text-orange-600' : '' }}">
                {{ $editingId ? 'Edit Periode' : 'Tambah Periode Baru' }}
            </h3>
            @if($editingId)
                <x-filament::button wire:click="cancelEdit" color="gray">
                    Batal Edit
                </x-filament::button>
            @endif
        </div>
        
        {{ $this->form }}

        <div class="mt-4 text-right">
            <x-filament::button wire:click="save" color="{{ $editingId ? 'warning' : 'primary' }}">
                {{ $editingId ? 'Simpan Perubahan' : 'Buat Periode' }}
            </x-filament::button>
        </div>
    </div>
</div>