<div 
  x-data 
    {{-- 1. Tangkap tombol Escape --}}
    @keydown.escape.window="$wire.closePreviewAndCleanup()"
    
    {{-- 2. Tangkap Klik di Luar (Backdrop) --}}
    {{-- Karena div ini adalah konten modal, area abu-abu dianggap 'outside' --}}
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
    <div class="w-full">
        <iframe 
            src="{{ route('preview.pdf', $token) }}"
            class="w-full h-[80vh]"
        ></iframe>
    </div>
</div>
