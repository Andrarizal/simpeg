<?php

namespace App\Filament\Resources\Overtimes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OvertimeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('staff.name')
                    ->numeric(),
                TextEntry::make('overtime_date')
                    ->date(),
                TextEntry::make('start_time')
                    ->time(),
                TextEntry::make('end_time')
                    ->time(),
                TextEntry::make('hours')
                    ->numeric(),
                TextEntry::make('reason'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
