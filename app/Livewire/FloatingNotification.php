<?php

namespace App\Livewire;

use Livewire\Component;

class FloatingNotification extends Component
{
    protected $listeners = [
        'database-notifications.sent' => '$refresh',
        'mark-expiry-read' => 'markAsReadSession'
    ];

    public function render()
    {
        return view('filament.components.floating-notification');
    }

    public function markAsReadSession()
    {
        session()->put('doc_expiry_notified', true);
    }
}
