<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StaffInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nik')
                    ->label('NIK'),
                TextEntry::make('name')
                    ->label('Nama Lengkap'),
                TextEntry::make('birth_place')
                    ->label('Tempat Lahir'),
                TextEntry::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date(),
                TextEntry::make('sex')
                    ->label('Jenis Kelamin'),
                TextEntry::make('phone')
                    ->label('No. Telepon'),
                TextEntry::make('personal_email')
                    ->label('Email Pribadi'),
                TextEntry::make('office_email')
                    ->label('Email Kantor'),
                TextEntry::make('last_education')
                    ->label('Pendidikan Terakhir'),
                TextEntry::make('work_entry_date')
                    ->label('Tanggal Masuk Kerja')
                    ->date(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
