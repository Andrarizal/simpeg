<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        $user = Auth::user();
        return 'Selamat Datang, ' . $user->name;
    }
    
    public function getColumns(): int | array
    {
        return 2;
    }
}