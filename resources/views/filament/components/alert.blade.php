@php
    $colorClass = match($color ?? 'warning') {
        'danger'  => 'bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-500',
        'success' => 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-500',
        'info'    => 'bg-info-50 text-info-600 dark:bg-info-500/10 dark:text-info-500',
        default   => 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-500',
    };
@endphp

<div class="space-y-4"
    x-data 
    @keydown.escape.window="$wire.closePreviewAndCleanup()"
    x-on:click.outside="$wire.closePreviewAndCleanup()"
>
    <div class="flex items-center justify-between mb-5 border-b pb-5 -mx-6 px-6">
        <h2 class="text-xl font-bold tracking-tight">Preview PDF</h2>
        
        <button wire:click="closePreviewAndCleanup" 
            type="button"
            class="text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <div class="flex items-center gap-x-3 rounded-lg p-4 text-sm {{ $colorClass }}">
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
        </svg>
        <span class="font-medium">{{ $message }}</span>
    </div>
</div>