@props(['chair', 'isRoot' => false])

<div @class(['hv-child-branch' => !$isRoot, 'flex items-center'])>
    
    {{-- CARD JABATAN --}}
    <div class="hv-card group" @if($isRoot) id="org-root-node" @endif>
        <div class="font-bold text-sm text-primary-600 dark:text-primary-400">
            {{ $chair->name }}
        </div>
    </div>

    {{-- BAGIAN BAWAHAN (Gunakan 'subordinates', bukan 'children') --}}
    @if($chair->subordinates->count() > 0)
        
        <div class="hv-connector"></div>

        <div class="hv-children">
            @foreach($chair->subordinates as $child)
                @include('filament.pages.partials.org-chart-node', ['chair' => $child, 'isRoot' => false])
            @endforeach
        </div>
    @endif

</div>