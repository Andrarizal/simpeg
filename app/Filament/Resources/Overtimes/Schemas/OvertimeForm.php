<?php

namespace App\Filament\Resources\Overtimes\Schemas;

use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class OvertimeForm
{
    public static function configure(Schema $schema): Schema
    {
        $staff = Auth::user()->staff;
        $chair = !$staff ? 1 : $staff->chair_id;

        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('Rencana Pelaksanaan')
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
                                    ->description('Detail tanggal dan uraian tugas yang akan dikerjakan.')
                                    ->icon('heroicon-m-clipboard-document-list')
                                    ->schema([
                                        DatePicker::make('overtime_date')
                                            ->label('Tanggal Lembur')
                                            ->prefixIcon('heroicon-m-calendar-days')
                                            ->minDate(fn () => Carbon::today())
                                            ->maxDate(date('Y-12-31'))
                                            ->displayFormat('d F Y')
                                            ->required()
                                            ->native(false)
                                            ->live()
                                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                                $staffId = $get('staff_id');
                                                if (! $state || ! $staffId) {
                                                    return;
                                                }
                                                $schedule = Schedule::query()
                                                    ->where('staff_id', $staffId)
                                                    ->where('schedule_date', $state)
                                                    ->with('shift') 
                                                    ->first();
                                                if ($schedule && $schedule->shift) {
                                                    $jamPulang = Carbon::parse($schedule->shift->end_time)->format('H:i');
        
                                                    $set('start_time', $jamPulang);
                                                } else {
                                                    $set('start_time', null);
                                                }
                                            })
                                            ->columnSpanFull(),

                                        Textarea::make('command')
                                            ->label('Perintah / Uraian Tugas')
                                            ->placeholder('Jelaskan secara rinci tugas yang harus diselesaikan...')
                                            ->rows(5)
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),

                        Group::make()
                            ->schema([
                                Section::make('Rincian Lembur')
                                    ->icon('heroicon-m-user')
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
                                        Select::make('staff_id')
                                            ->label('Nama Pegawai')
                                            ->relationship('staff', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->default(fn() => $staff?->id)
                                            ->disabled(fn() => $chair > 1) 
                                            ->dehydrated(true)
                                            ->native(false),

                                        TimePicker::make('start_time')
                                            ->label('Waktu Mulai')
                                            ->prefixIcon('heroicon-m-play')
                                            ->native(false)
                                            ->displayFormat('H:i')
                                            ->required()
                                            ->seconds(false)
                                            ->disabled()
                                            ->dehydrated(true),

                                        TimePicker::make('end_time')
                                            ->label('Waktu Selesai')
                                            ->prefixIcon('heroicon-m-stop')
                                            ->helperText(new HtmlString('
                                                <span class="text-xs -mt-1">
                                                    Terisi setelah lembur selesai
                                                </span>
                                            '))
                                            ->native(false)
                                            ->displayFormat('H:i')
                                            ->disabled()
                                            ->seconds(false),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])->columnSpanFull(),
            ]);
    }
}
