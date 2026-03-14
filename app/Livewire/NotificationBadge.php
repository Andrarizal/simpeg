<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBadge extends Component
{
    public function render()
    {
        $unreadCount = Auth::user()->unreadNotifications()->count();
        
        return view('livewire.notification-badge', [
            'unreadCount' => $unreadCount
        ]);
    }
}
