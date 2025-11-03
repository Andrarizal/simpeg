<?php

namespace App\Filament\Resources\Leaves\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LeaveInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('type')
                    ->label('Jenis'),
                TextEntry::make('staff.name')
                    ->label('Nama Pegawai'),
                TextEntry::make('start_date')
                    ->label('Dari Tanggal')
                    ->date(),
                TextEntry::make('end_date')
                    ->label('Sampai Tanggal')
                    ->date(),
                TextEntry::make('remaining')
                    ->label('Sisa Cuti Sebelum Pengajuan')
                    ->visible()
                    ->numeric(),
                TextEntry::make('replacement.name')
                    ->label('Nama Pengganti'),
                TextEntry::make('status'),
                TextEntry::make('approver.name')
                    ->label('Telah direspon oleh'),
                TextEntry::make('adverb')
                    ->label('Catatan'),
                TextEntry::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->date(),
            ]);
    }
}
