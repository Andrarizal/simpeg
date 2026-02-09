<?php

namespace App\Filament\Resources\Performances;

use App\Filament\Resources\Performances\Pages\ManagePerformances;
use App\Models\PerformancePeriod;
use App\Models\Staff;
use App\Models\StaffPerformance;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PerformanceResource extends Resource
{
    protected static ?string $model = StaffPerformance::class;

    protected static ?string $modelLabel = 'Penilaian Kinerja';       
    protected static ?string $pluralModelLabel = 'Penilaian Kinerja'; 
    protected static ?string $navigationLabel = 'Penilaian Kinerja';
    protected static ?int $navigationSort = 3;
    protected static UnitEnum|string|null $navigationGroup = 'Keperluan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartLine;

    protected static ?string $recordTitleAttribute = 'Performance';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Capaian')
                    ->description('Isi detail capaian kinerja Anda untuk periode ini.')
                    ->icon('heroicon-m-document-text')
                    ->extraAttributes([
                        'class' => implode(' ', [
                            '[&_.fi-section-header]:bg-gradient-to-br',
                            '[&_.fi-section-header]:from-emerald-500',
                            '[&_.fi-section-header]:to-teal-600',
                            '[&_.fi-section-header]:dark:from-emerald-900',
                            '[&_.fi-section-header]:dark:to-teal-950',
                            '[&_.fi-section-header]:rounded-t-2xl',
                            '[&_.fi-section-header-heading]:!text-white',
                            '[&_.fi-section-header-description]:!text-white/80',
                            '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                        ])
                    ])
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('staff_id')
                                ->label('Nama Pegawai')
                                ->prefixIcon('heroicon-m-user')
                                ->options(Staff::all()->pluck('name', 'id'))
                                ->default(Auth::user()->staff_id)
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            Select::make('period_id')
                                ->label('Periode')
                                ->prefixIcon('heroicon-m-calendar')
                                ->options(function(){
                                    return PerformancePeriod::query()
                                        ->orderBy('start_date', 'desc')
                                        ->get()
                                        ->mapWithKeys(function ($period) {
                                            $start = Carbon::parse($period->start_date)->translatedFormat('M');
                                            $end = Carbon::parse($period->end_date)->translatedFormat('M Y');
                                            return [$period->id => "$start - $end"];
                                        });
                                })
                                ->default(fn() => PerformancePeriod::where('status', 1)->latest()->value('id'))
                                ->disabled()
                                ->dehydrated()
                                ->selectablePlaceholder(false)
                                ->required(),
                        ]),

                        TextInput::make('title')
                            ->label('Judul Capaian')
                            ->placeholder('Contoh: Laporan Kinerja Bulan Maret')
                            ->maxLength(255)
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Deskripsi Capaian')
                            ->placeholder('Jelaskan poin-poin utama capaian Anda...')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),
                    ])
                ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                ->schema([
                    Section::make('Capaian dan Hasil')
                        ->icon('heroicon-m-clipboard-document-check')
                        ->extraAttributes([
                            'class' => implode(' ', [
                                '[&_.fi-section-header]:bg-gradient-to-br',
                                '[&_.fi-section-header]:from-emerald-500',
                                '[&_.fi-section-header]:to-teal-600',
                                '[&_.fi-section-header]:dark:from-emerald-900',
                                '[&_.fi-section-header]:dark:to-teal-950',
                                '[&_.fi-section-header]:rounded-t-2xl',
                                '[&_.fi-section-header-heading]:!text-white',
                                '[&_.fi-section-header-description]:!text-white/80',
                                '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                            ])
                        ])
                        ->schema([
                            TextEntry::make('title')
                                ->hiddenLabel() 
                                ->weight(FontWeight::Bold)
                                ->size(TextSize::Large)
                                ->columnSpanFull(),

                            TextEntry::make('description')
                                ->hiddenLabel()
                                ->markdown()
                                ->prose() 
                                ->columnSpanFull()
                                ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-900 p-2 px-4 -mt-4 rounded-2xl border border-gray-200 dark:border-gray-800']),
                            Grid::make()
                                ->schema([
                                    TextEntry::make('score_result')
                                    ->label('Nilai Rata-rata')
                                    ->state(function ($record) {
                                        $avg = $record->appraisal()->avg('score'); 
                                        return $avg ? number_format($avg, 1) : '-';
                                    })
                                    ->badge()
                                    ->size(TextSize::Large)
                                    ->color(fn ($state) => match (true) {
                                        $state >= 85 => 'info', 
                                        $state >= 70 => 'success', 
                                        $state >= 50 => 'warning', 
                                        $state > 0   => 'danger',  
                                        default      => 'gray',
                                    }),

                                TextEntry::make('appraiser_info')
                                    ->label('Telah Dinilai Oleh')
                                    ->icon('heroicon-m-shield-check')
                                    ->state(function ($record) {
                                        if (! $record->appraisal) {
                                            return 'Belum Dinilai';
                                        } 
                                        
                                        $level = $record->appraisal->appraiser->chair->level ?? null;

                                        return match ($level) {
                                            4 => 'Assesor Tingkat 1',
                                            3 => 'Assesor Tingkat 2',
                                            2 => 'Assesor Tingkat 3',
                                            1 => 'Assesor Tingkat 4',
                                            default => 'Staff / Lainnya',
                                        };
                                    })
                                    ->color(fn ($state) => $state == 'Belum Dinilai' ? 'gray' : 'primary'),  
                                ])
                        ])
                        ->columnSpan(['lg' => 2]),
                    Section::make()
                        ->schema([
                            TextEntry::make('staff.name')
                                ->label('Pegawai Dinilai')
                                ->icon('heroicon-m-user-circle')
                                ->weight(FontWeight::Bold)
                                ->size(TextSize::Large),

                            TextEntry::make('period_id')
                                ->label('Periode')
                                ->icon('heroicon-m-calendar-days')
                                ->formatStateUsing(fn ($record) => 
                                    Carbon::parse($record->period->start_date)->translatedFormat('M') . ' - ' . 
                                    Carbon::parse($record->period->end_date)->translatedFormat('M Y')
                                )
                                ->badge()
                                ->color('info'),

                            TextEntry::make('created_at')
                                ->label('Tanggal Input')
                                ->icon('heroicon-m-clock')
                                ->date('d F Y')
                                ->size(TextSize::Small)
                                ->color('gray'),
                        ])
                        ->columnSpan(['lg' => 1]),
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
