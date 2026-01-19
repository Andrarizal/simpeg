<div class="w-full h-full flex flex-col" style="min-height: 75vh;">
    {{-- Gunakan tag Object untuk memaksa render PDF --}}
    <object
        data="{{ $url }}"
        type="application/pdf"
        width="100%"
        height="100%"
        class="flex-1 w-full h-full rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm"
    >
        {{-- Fallback jika browser tidak mendukung tag Object, gunakan Iframe --}}
        <iframe
            src="{{ $url }}"
            width="100%"
            height="100%"
            class="flex-1 w-full h-full"
            frameborder="0"
        >
            {{-- Fallback terakhir jika device tidak support PDF viewer sama sekali --}}
            <div class="flex flex-col items-center justify-center h-full p-6 text-center text-gray-500">
                <p class="mb-4">Browser Anda tidak dapat menampilkan preview PDF ini.</p>
                <a 
                    href="{{ $url }}" 
                    target="_blank" 
                    class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-500"
                >
                    Download File Saja
                </a>
            </div>
        </iframe>
    </object>
</div>