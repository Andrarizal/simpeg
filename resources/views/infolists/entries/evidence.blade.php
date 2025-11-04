@php
    use Illuminate\Support\Facades\Storage;

    $url = $getState() ? Storage::disk('public')->url($getState()) : null;
@endphp

@if ($url)
    @if (Str::endsWith($getState(), ['.jpg', '.jpeg', '.png', '.webp']))
        <div class="space-y-2">
            <img src="{{ $url }}" alt="Surat Cuti" class="rounded-lg w-32 border" />
            <a href="{{ $url }}" target="_blank"
               class="inline-flex items-center gap-1 text-sm text-primary-600 hover:underline">
                <x-heroicon-o-eye class="w-4 h-4" /> Lihat Gambar
            </a>
        </div>
    @elseif (Str::endsWith($getState(), '.pdf'))
        <a href="{{ $url }}" target="_blank"
           class="inline-flex items-center gap-1 text-sm text-primary-600 hover:underline">
            <x-heroicon-o-document-text class="w-4 h-4" /> Lihat PDF
        </a>
    @else
        <a href="{{ $url }}" target="_blank"
           class="inline-flex items-center gap-1 text-sm text-primary-600 hover:underline">
            <x-heroicon-o-paper-clip class="w-4 h-4" /> Lihat File
        </a>
    @endif
@else
    <span class="text-gray-500 text-sm italic">Belum ada surat cuti</span>
@endif
