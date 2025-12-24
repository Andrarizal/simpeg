<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\Staff\StaffResource;
use App\Models\Staff;
use App\Models\WorkHistory;
use BackedEnum;
use Carbon\Carbon;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ManageWorkHistories extends ManageRelatedRecords
{
    protected static string $resource = StaffResource::class;

    protected static string $relationship = 'workHistories';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $title = 'Riwayat Jabatan Pegawai';

    public function getSubheading(): string|Htmlable|null
    {
        $staff = Staff::where('id', $this->record->id)->first();

        $nameStaff = "
            <div class='flex items-center gap-1 whitespace-nowrap bg-gray-100 dark:bg-white/5 px-2 py-1 rounded-md border border-gray-200 dark:border-white/10'>
                <span class='font-bold text-primary-600 dark:text-primary-400'>Nama:</span>
                <span class='text-gray-700 dark:text-gray-300'> $staff->name</span>
            </div>
        ";

        return new HtmlString("
            <div class='flex flex-wrap items-center gap-2 mt-2 text-xs'>
                <div class='flex items-center justify-center w-6 h-6 bg-gray-100 dark:bg-gray-800 rounded-full shrink-0'>
                    <svg class='w-4 h-4 text-gray-500' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'>
                        <path fill-rule='evenodd' clip-rule='evenodd' d='M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z' />
                    </svg>
                </div>
                
                {$nameStaff}
            </div>
        ");
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('start_date')
                    ->label('Periode')
                    ->formatStateUsing(fn ($record) => 
                        Carbon::parse($record->start_date)->format('d M Y') . ' s/d ' . ($record->end_date ? Carbon::parse($record->end_date)->format('d M Y') : 'Sekarang')
                    ),
                TextColumn::make('chair.name')
                    ->label('Jabatan'),
                TextColumn::make('unit.name')
                    ->label('Unit'),
                TextColumn::make('staffStatus.name')
                    ->label('Status Pegawai'),
                TextColumn::make('decree_number')
                    ->label('No SK')
                    ->formatStateUsing(fn ($state) => $state ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('decree_date')
                    ->label('Tanggal SK')
                    ->date()
                    ->formatStateUsing(fn ($state) => $state ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('class')
                    ->label('Golongan')
                    ->date()
                    ->formatStateUsing(fn ($state) => $state ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('decree')
                    ->label('File SK')
                    ->formatStateUsing(fn() => 'Unduh')
                    ->url(fn ($record) => $record->decree ? asset('storage/'.$record->decree) : null)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_date', 'desc');
    }
}