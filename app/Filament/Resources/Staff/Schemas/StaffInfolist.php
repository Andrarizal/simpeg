<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StaffInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nik')
                    ->label('NIK'),
                TextEntry::make('nip')
                    ->label('NIP'),
                TextEntry::make('name')
                    ->label('Nama Lengkap'),
                TextEntry::make('birth_place')
                    ->label('Tempat Lahir'),
                TextEntry::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date(),
                TextEntry::make('sex')
                    ->label('Jenis Kelamin'),
                TextEntry::make('marital')
                    ->label('Status Perkawinan'),
                TextEntry::make('address')
                    ->label('Alamat'),
                TextEntry::make('email')
                    ->label('Email Pribadi'),
                TextEntry::make('phone')
                    ->label('No. Telepon'),
                TextEntry::make('other_phone')
                    ->label('No. Telepon Kerabat'),
                TextEntry::make('other_phone_adverb')
                    ->label('Hubungan dengan Kerabat'),
                TextEntry::make('entry_date')
                    ->label('Tanggal Masuk Kerja')
                    ->date(),
                TextEntry::make('retirement_date')
                    ->label('Tanggal Pensiun')
                    ->date(),
                TextEntry::make('staffStatus.name')
                    ->label('Status Kepegawaian'),
                TextEntry::make('chair.name')
                    ->label('Jabatan'),
                TextEntry::make('group.name')
                    ->label('Kelompok Tenaga Kerja'),
                TextEntry::make('unit.name')
                    ->label('Unit Kerja'),
                    // --- KONTRAK KERJA ---
                TextEntry::make('contract.contract_number')
                    ->label('Nomor Kontrak')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->contract),
                TextEntry::make('contract.start_date')
                    ->label('Tanggal Mulai Kontrak')
                    ->date()
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->contract),
                TextEntry::make('contract.end_date')
                    ->label('Tanggal Berakhir Kontrak')
                    ->date()
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->contract),

                // --- PENGANGKATAN ---
                TextEntry::make('appointment.decree_number')
                    ->label('Nomor SK Pengangkatan')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->appointment),
                TextEntry::make('appointment.decree_date')
                    ->label('Tanggal SK Pengangkatan')
                    ->date()
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->appointment),
                TextEntry::make('appointment.class')
                    ->label('Golongan')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->appointment),

                // --- PENYESUAIAN ---
                TextEntry::make('adjustment.decree_number')
                    ->label('Nomor SK Penyesuaian')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->adjustment),
                TextEntry::make('adjustment.decree_date')
                    ->label('Tanggal SK Penyesuaian')
                    ->date()
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->adjustment),
                TextEntry::make('adjustment.class')
                    ->label('Golongan Setelah Penyesuaian')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->adjustment),

                // --- PENDIDIKAN SAAT MASUK ---
                TextEntry::make('entryEducation.level')
                    ->label('Jenjang Pendidikan Saat Masuk')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->entryEducation),
                TextEntry::make('entryEducation.institution')
                    ->label('Institusi Pendidikan Saat Masuk')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->entryEducation),
                TextEntry::make('entryEducation.certificate_number')
                    ->label('Nomor Ijazah')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->entryEducation),
                TextEntry::make('entryEducation.certificate_date')
                    ->label('Tanggal Ijazah')
                    ->date()
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->entryEducation),
                TextEntry::make('entryEducation.nonformal_education')
                    ->label('Pendidikan Nonformal')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->entryEducation),
                TextEntry::make('entryEducation.adverb')
                    ->label('Keterangan Pendidikan')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->entryEducation),

                // --- PENDIDIKAN SAAT BEKERJA ---
                TextEntry::make('workEducation.level')
                    ->label('Jenjang Pendidikan Saat Bekerja')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->workEducation),
                TextEntry::make('workEducation.major')
                    ->label('Jurusan Pendidikan')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->workEducation),
                TextEntry::make('workEducation.institution')
                    ->label('Institusi Pendidikan')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->workEducation),
                TextEntry::make('workEducation.certificate_number')
                    ->label('Nomor Ijazah')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->workEducation),
                TextEntry::make('workEducation.certificate_date')
                    ->label('Tanggal Ijazah')
                    ->date()
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->workEducation),

                // --- PENGALAMAN KERJA ---
                TextEntry::make('workExperience.institution')
                    ->label('Institusi Pengalaman Kerja')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->workExperience),
                TextEntry::make('workExperience.work_length')
                    ->label('Lama Bekerja')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->workExperience),
                TextEntry::make('workExperience.admission')
                    ->label('Keterangan')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record->workExperience),
            ]);
    }
}
