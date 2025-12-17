<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeMessage extends Widget
{
    protected static ?int $sort = 1;

    protected string $view = 'filament.widgets.welcome-message';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = Auth::user();
        $staff = $user->staff; // Eager load di model user lebih baik

        $masaKerja = '-';
        $countdownPensiun = '-';
        
        // Inisial Nama
        $initials = collect(explode(' ', $user->name))
            ->map(fn ($word) => mb_substr($word, 0, 1))
            ->take(2)
            ->join('');

        if ($staff) {
            if ($staff->entry_date) {
                $masaKerja = number_format(Carbon::parse($staff->entry_date)->diffInYears(Carbon::now()), 1);
            }
            if ($staff->birth_date) {
                $umurPensiun = 58;
                $age = Carbon::parse($staff->birth_date)->age;
                $countdownPensiun = max($umurPensiun - $age, 0);
            }
        }

        return compact('user', 'staff', 'masaKerja', 'countdownPensiun', 'initials');
    }
}
