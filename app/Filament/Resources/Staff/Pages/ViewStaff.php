<?php

namespace App\Filament\Resources\Staff\Pages;

use App\Filament\Resources\Staff\StaffResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStaff extends ViewRecord
{
    protected static string $resource = StaffResource::class;

    protected static ?string $title = 'Detail Pegawai';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('history')
                ->label('Lihat Riwayat Jabatan')
                ->icon('heroicon-o-clock')
                ->color('info')
                ->url(fn ($record) => StaffResource::getUrl('history', ['record' => $record])),
            EditAction::make(),
        ];
    }
}
