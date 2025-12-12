@props(['date', 'options']) 

@php
    $currentValue = $getState() ?? "-";
    
    $recordModel = $getRecord(); 
    $staffId = $recordModel->id;

    $isDisabled = $isDisabled();
@endphp

<div wire:key="select-{{ $staffId }}-{{ $date }}" class="w-full h-full flex items-center justify-center mx-2 min-w-[60px]">
    <select
        @disabled($isDisabled)
        {{-- Trigger update ke PHP --}}
        @if(!$isDisabled)
          wire:change="updateShift('{{ $staffId }}', '{{ $date }}', $event.target.value)"
        @endif
        
        {{-- Matikan input saat sedang menyimpan (Loading) --}}
        wire:target="updateShift"
        wire:loading.attr="disabled"
        
        {{-- Styling Native Filament V3 (Dark/Light Mode Compatible) --}}
        class="
            block w-full h-full py-2 px-1 text-center text-sm transition duration-250
            bg-gray-200 
            hover:bg-gray-300
            text-gray-950 rounded-xl
            focus:ring-1 focus:ring-gray-400 focus:border-0
            disabled:text-gray-400 disabled:cursor-not-allowed
            
            dark:text-white 
            dark:bg-gray-800 
            dark:hover:bg-gray-700 
            dark:focus:ring-0
            dark:focus:border-0
            
            cursor-pointer 
            appearance-none {{-- Menghilangkan panah default browser agar rapi --}}
        "
        style="-webkit-appearance: none; -moz-appearance: none;" 
    >
        {{-- Opsi Default (Strip) --}}
        <option value="-" class="text-gray-500 dark:text-gray-400">-</option>

        {{-- Loop Options --}}
        @foreach($options as $key => $label)
            <option 
                value="{{ $key }}" 
                @selected((string)$key === (string)$currentValue)
                {{-- Styling dropdown list options (Browser behavior varies here, but giving hint) --}}
                class="bg-gray-100 text-gray-950 dark:bg-gray-900 dark:text-white radius-xl"
            >
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>