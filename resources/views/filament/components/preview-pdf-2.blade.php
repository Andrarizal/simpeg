<div class="flex flex-col h-[80vh]">
    <div class="flex-1 w-full bg-gray-100 rounded-lg overflow-hidden relative">
        @if(!empty($url))
            <object data="{{ $url }}" type="application/pdf" class="w-full h-full" frameborder="0"></object>
        @else
            <div class="flex items-center justify-center h-full text-gray-500">
                File tidak ditemukan.
            </div>
        @endif
    </div>
</div>