<?php

namespace App\Filament\Resources\Leaves\Schemas;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LeaveInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('type')
                    ->label('Jenis')
                    ->formatStateUsing(fn ($record) => $record->type . ' ' . $record->subtype),
                TextEntry::make('staff.name')
                    ->label('Nama Pegawai'),
                TextEntry::make('start_date')
                    ->label('Dari Tanggal')
                    ->date(),
                TextEntry::make('end_date')
                    ->label('Sampai Tanggal')
                    ->date(),
                TextEntry::make('reason')
                    ->label('Keperluan (Alasan)'),
                TextEntry::make('remaining')
                    ->label(fn ($record) => 'Sisa ' . $record->type . ' Sebelumnya')
                    ->visible(fn ($record) => $record->subtype === 'Tahunan' || $record->subtype === 'Tahunan' ? true : false)
                    ->numeric(),
                TextEntry::make('replacement.name')
                    ->label('Nama Pengganti')
                    ->formatStateUsing(fn ($state, $record) => 
                        $state. ' ' .
                        ($record->is_replaced === 1 ?
                            '(Bersedia)' :
                            ($record->is_replaced === 0 ?
                                '(Tidak Bersedia)' : '(Belum Konfirmasi)'))),
                TextEntry::make('evidence')
                    ->label('Surat Cuti')
                    ->visible(fn ($state) => $state ? true : false)
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->url(fn ($record) => asset('storage/' . $record->evidence))
                            ->openUrlInNewTab()
                            ->color('success')
                            ->outlined()
                    ),
                TextEntry::make('status')
                    ->formatStateUsing(function ($state, $record) {
                        if ($state === 'Disetujui Kepala Seksi' && optional($record->staff->chair)->level == 3) {
                            return 'Diketahui Kepala Seksi';
                        }
                        return $state;
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Diketahui Kepala Unit' => 'info',
                        'Diketahui Koordinator' => 'info',
                        'Disetujui Kepala Seksi', 'Diketahui Kepala Seksi' => 'success',
                        'Disetujui Direktur' => 'success',
                        'Ditolak' => 'danger',
                    }),
                TextEntry::make('approver.name')
                    ->label('Telah direspon oleh')
                    ->visible(fn ($state) => $state ? true : false),
                TextEntry::make('adverb')
                    ->label('Catatan')
                    ->visible(fn ($state) => $state ? true : false),
                TextEntry::make('is_verified')
                    ->label('Verifikasi SDM')
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->is_verified === 1
                            ? '(Terverifikasi)'
                            : '(Tidak Terverifikasi)')
                    )
                    ->placeholder('(Belum Terverifikasi)'),
                TextEntry::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->date(),
            ]);
    }
}
