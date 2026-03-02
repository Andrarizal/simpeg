<?php

namespace App\Filament\Resources\Duties\Schemas;

use App\Models\Duty;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DutyForm
{
    public static function generateLetterNumber(): string
    {
        $year = now()->year;
        $month = now()->month;

        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $romanMonth = $romans[$month] ?? 'I';

        $lastLetter = Duty::query()
            ->whereYear('created_at', $year)
            ->latest('created_at')
            ->first();

        $newSequence = 1;

        if ($lastLetter) {
            $parts = explode('/', $lastLetter->reference_number);
            
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $newSequence = (int) $parts[0] + 1;
            }
        }

        $number = "{$newSequence}/RSU-MP/{$romanMonth}/{$year}";
        return $number;
    }
    
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Identitas Penugasan')
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
                        Grid::make(1)->schema([
                            TextInput::make('reference_number')
                                ->label('Nomor Surat')
                                ->readOnly()
                                ->default(function () {
                                    return self::generateLetterNumber();
                                })
                                ->dehydrated()
                                ->required(),

                            DatePicker::make('duty_date')
                                ->label('Tanggal Acara')
                                ->minDate(today())
                                ->required(),

                            Grid::make(2)
                                ->schema([
                                    TimePicker::make('start_time')
                                        ->label('Waktu Mulai')
                                        ->native(false)
                                        ->displayFormat('H:i')
                                        ->seconds(false)
                                        ->required(),
        
                                    TimePicker::make('end_time')
                                        ->label('Waktu Selesai')
                                        ->native(false)
                                        ->seconds(false)
                                        ->displayFormat('H:i'),
                                ]),
                                    
                            TextInput::make('location')
                                ->label('Lokasi Acara')
                                ->required(),

                            Textarea::make('duty')
                                ->label('Acara')
                                ->rows(2)
                                ->required(),
                            ]),

                            TextInput::make('transportation')
                                ->label('Alat Transportasi')
                                ->datalist([
                                    'Kendaraan Pribadi',
                                    'Ambulans',
                                ])
                                ->autocomplete(false)
                                ->required(),
                    ])
                    ->columnSpan(1),

                Section::make('Distribusi / Tujuan Surat')
                    ->description('Pilih staf yang akan menerima notifikasi dan akses ke surat ini.')
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
                        CheckboxList::make('targetStaffs')
                            ->hiddenLabel()
                            ->relationship('targetStaffs', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "{$record->name} — " . ($record->chair->name ?? 'Tanpa Jabatan') . " (" . ($record->unit->name ?? 'Tanpa Unit') . ")"
                            )
                            ->columns(1) 
                            ->gridDirection('row')
                            ->searchable() 
                            ->bulkToggleable()
                            ->required()
                            ->extraAttributes([
                                'class' => 'max-h-64 lg:max-h-105 overflow-y-auto border border-gray-200 dark:border-white/10 rounded-2xl p-4 shadow-sm'
                            ]),
                        ])
                    ->columnSpan(2),
            ]);
    }
}
