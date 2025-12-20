<?php

namespace App\Filament\Resources\StaffAdministrations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class StaffAdministrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('staff.name')
                    ->relationship('staff', 'name')
                    ->label('Nama Pegawai')
                    ->disabled()
                    ->required()
                    ->inlineLabel()
                    ->columnSpanFull(),
                FileUpload::make('sip')
                    ->label('Surat Izin Praktek')
                    ->disk('public')
                    ->visibility('public')
                    ->directory('sip')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(2048)
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->helperText('Unggah SIP dalam format PDF'),
                DatePicker::make('sip_expiry')
                    ->label('Masa Berlaku SIP')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->inlineLabel()
                    ->columnSpanFull(),
                FileUpload::make('str')
                    ->label('Surat Tanda Registrasi')
                    ->disk('public')
                    ->visibility('public')
                    ->directory('str')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(2048)
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->helperText('Unggah STR dalam format PDF'),
                DatePicker::make('str_expiry')
                    ->label('Masa Berlaku STR')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->inlineLabel()
                    ->columnSpanFull(),
                FileUpload::make('mcu')
                    ->label('Medical Check Up')
                    ->disk('public')
                    ->visibility('public')
                    ->directory('mcu')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(2048)
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->helperText('Unggah MCU dalam format PDF'),
                DatePicker::make('mcu_expiry')
                    ->label('Masa Berlaku MCU')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->inlineLabel()
                    ->columnSpanFull(),
                FileUpload::make('spk')
                    ->label('Surat Penugasan Klinis')
                    ->disk('public')
                    ->visibility('public')
                    ->directory('spk')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(2048)
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->helperText('Unggah SPK dalam format PDF'),
                DatePicker::make('spk_expiry')
                    ->label('Masa Berlaku SPK')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->inlineLabel()
                    ->columnSpanFull(),
                FileUpload::make('rkk')
                    ->label('Rincian Kewenangan Klinis')
                    ->disk('public')
                    ->visibility('public')
                    ->directory('rkk')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(2048)
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->helperText('Unggah RKK dalam format PDF'),
                DatePicker::make('rkk_expiry')
                    ->label('Masa Berlaku RKK')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->inlineLabel()
                    ->columnSpanFull(),
                FileUpload::make('utw')
                    ->label('Uraian Tugas dan Wewenang')
                    ->disk('public')
                    ->visibility('public')
                    ->directory('utw')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(2048)
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->helperText('Unggah UTW dalam format PDF'),
                DatePicker::make('utw_expiry')
                    ->label('Masa Berlaku UTW')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->inlineLabel()
                    ->columnSpanFull(),
            ]);
    }
}
