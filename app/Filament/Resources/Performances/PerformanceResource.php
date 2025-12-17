<?php

namespace App\Filament\Resources\Performances;

use App\Filament\Resources\Performances\Pages\ManagePerformances;
use App\Models\StaffPerformance;
use BackedEnum;
use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class PerformanceResource extends Resource
{
    protected static ?string $model = StaffPerformance::class;

    protected static ?string $modelLabel = 'Penilaian Kinerja';       
    protected static ?string $pluralModelLabel = 'Penilaian Kinerja'; 
    protected static ?string $navigationLabel = 'Penilaian Kinerja';
    protected static ?int $navigationSort = 8;
    protected static UnitEnum|string|null $navigationGroup = 'Kepegawaian';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartLine;

    protected static ?string $recordTitleAttribute = 'Performance';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('staff.name')
                            ->label('Nama Pegawai')
                            ->inlineLabel(),
                        TextEntry::make('period_id')
                            ->label('Periode Penilaian')
                            ->formatStateUsing(fn ($record) => 
                                Carbon::parse($record->period->start_date)->translatedFormat('F') . ' - ' . 
                                Carbon::parse($record->period->end_date)->translatedFormat('F Y')
                            )
                            ->inlineLabel(),
                        TextEntry::make('title')
                            ->label('Judul Capaian')
                            ->inlineLabel(),
                        TextEntry::make('description')
                            ->label('Deskripsi Capaian')
                            ->inlineLabel(),
                        TextEntry::make('created_at')
                            ->label('Dibuat pada')
                            ->date('d F Y')
                            ->inlineLabel(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePerformances::route('/'),
        ];
    }
}
