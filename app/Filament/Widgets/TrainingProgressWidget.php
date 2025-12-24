<?php

namespace App\Filament\Widgets;

use App\Models\StaffTraining;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TrainingProgressWidget extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.training-progress-widget';

    public function getViewData(): array
    {
        $user = Auth::user();

        $total_hours = $user->staff->training()
            ->whereYear('training_date', now()->year)
            ->sum('duration');

        $target = 20;
        $percentage = min(100, ($total_hours / $target) * 100);

        return [
            'total_hours' => $total_hours,
            'target' => $target,
            'percentage' => $percentage,
        ];
    }
}
