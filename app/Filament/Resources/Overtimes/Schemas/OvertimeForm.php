<?php

namespace App\Filament\Resources\Overtimes\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class OvertimeForm
{
    public static function configure(Schema $schema): Schema
    {
        // Cek jabatan user
        $staff = Auth::user()->staff;
        // Cek jika superadmin
        $chair = !$staff ? 1 : $staff->chair_id;
        
        return $schema
            ->components([
                Select::make('staff_id')
                    ->label('Nama Pegawai')
                    ->relationship('staff', 'name')
                    ->required()
                    ->default(fn() => $staff->id)
                    ->disabled(fn() => $chair > 1 ? true : false)
                    ->dehydrated(true),
                DatePicker::make('overtime_date')
                    ->label('Tanggal')
                    ->default(fn () => Carbon::today())
                    ->minDate(fn () => Carbon::today())
                    ->maxDate(date('Y-12-31'))
                    ->required()
                    ->reactive()
                    ->native(false),
                TimePicker::make('start_time')
                    ->label('Waktu Mulai')
                    ->placeholder('Isi dengan jam pulang kerja')
                    ->required()
                    ->seconds(false)
                    ->native(false),
                TimePicker::make('end_time')
                    ->label('Waktu Selesai')
                    ->visibleOn(['edit'])
                    ->helperText('Isi jam selesai ketika lembur sudah berakhir.')
                    ->seconds(false)
                    ->native(false),
                Textarea::make('command')
                    ->label('Perintah')
                    ->rows(3)
                    ->required(),
            ]);
    }
}
