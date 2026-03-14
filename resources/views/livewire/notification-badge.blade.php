<div wire:poll.5s>
    <div class="badge-notif-wrapper">
        @if($unreadCount > 0)
            <span class="badge-angka">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
            <span class="badge-ping"></span>
        @endif
    </div>
</div>