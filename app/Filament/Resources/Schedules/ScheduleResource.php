<?php

namespace App\Filament\Resources\Schedules;

use App\Filament\Resources\Schedules\Pages\ManageSchedules;
use App\Models\Chair;
use App\Models\Schedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $modelLabel = 'Jam Kerja';
    protected static ?string $pluralModelLabel = 'Jam Kerja'; 
    protected static ?string $navigationLabel = 'Jam Kerja';
    protected static ?int $navigationSort = 3;
    protected static UnitEnum|string|null $navigationGroup = 'Perusahaan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Clock;

    protected static ?string $recordTitleAttribute = 'Schedule';

    public static function getPages(): array
    {
        return [
            'index' => ManageSchedules::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        $isLeader = Auth::user()->staff->chair_id == Auth::user()->staff->unit?->leader_id;

        return Auth::user()->staff->chair->level == 4 && Auth::user()->staff->chair_id == $isLeader;
    }
}
