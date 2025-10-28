<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

class StaffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nik')
                    ->label('NIK')
                    ->placeholder('ex. 3321029920192099')
                    ->required(),
                TextInput::make('name')
                    ->label('Nama')
                    ->placeholder('ex. Tamam Muhammad')
                    ->required(),
                TextInput::make('birth_place')
                    ->label('Tempat Lahir')
                    ->placeholder('ex. Sleman')
                    ->required(),
                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->required(),
                ToggleButtons::make('sex')
                    ->label('Jenis Kelamin')
                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                    ->inline()
                    ->required(),
                TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel()
                    ->placeholder('ex. 081234567890')
                    ->required(),
                Textarea::make('address')
                    ->label('Alamat Lengkap')
                    ->placeholder('Jalan Chelsea, RT 20/RW 82, Kecamatan Liverpool, Kabupaten Manchester')
                    ->required()
                    ->columnSpanFull(),
                Select::make('last_education')
                    ->label('Pendidikan Terakhir')
                    ->options(['SMA' => 'SMA', 'D3' => 'D3', 'D4/S1' => 'D4/S1', 'S2' => 'S2', 'S3' => 'S3'])
                    ->required(),
                DatePicker::make('work_entry_date')
                    ->label('Tanggal Masuk Kerja')
                    ->required(),
                TextInput::make('personal_email')
                    ->label('Email Pribadi')
                    ->email()
                    ->placeholder('ex tamam@gmail.com')
                    ->required(),
                TextInput::make('office_email')
                    ->label('Email Kantor')
                    ->email()
                    ->placeholder('tamam@mitra.co.id')
                    ->required(),
                Select::make('unit_id')
                    ->label('Unit Kerja')
                    ->relationship('unit', 'name')
                    ->required(),
                Select::make('chair_id')
                    ->label('Jabatan')
                    ->relationship('chair', 'name')
                    ->required(),
                Checkbox::make('confirmation')
                ->label('Buat akun pengguna untuk karyawan ini')
                ->default(true),
            ]);
    }
}
