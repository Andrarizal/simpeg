<?php

namespace App\Filament\Resources\Presences;

use App\Filament\Resources\Presences\Pages\ManagePresences;
use App\Models\Presence;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class PresenceResource extends Resource
{
    protected static ?string $model = Presence::class;

    protected static ?string $modelLabel = 'Presensi';       
    protected static ?string $pluralModelLabel = 'Presensi'; 
    protected static ?string $navigationLabel = 'Presensi';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::FingerPrint;

    protected static ?string $recordTitleAttribute = 'Presence';

    public static function getPages(): array
    {
        return [
            'index' => ManagePresences::route('/'),
        ];
    }
}
