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

        $masaKerja = $user->staff->entry_date
            ? number_format(Carbon::parse($user->staff->entry_date)->diffInYears(Carbon::now()), 1) : '-';

        $umurPensiun = 58;
        $countdownPensiun = $user->staff->birth_date
            ? $umurPensiun - Carbon::parse($user->staff->birth_date)->age : '-';

        return compact('user', 'masaKerja', 'countdownPensiun');
    }
}
