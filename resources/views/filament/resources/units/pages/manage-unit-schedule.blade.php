<x-filament-panels::page>
    <div 
        x-data 
        x-on:rebuild-table.window="$wire.$refresh()"
    >
        {{ $this->table }}
    </div>
</x-filament-panels::page>