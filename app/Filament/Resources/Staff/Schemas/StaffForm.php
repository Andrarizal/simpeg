<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                    ->maxLength(16)
                    ->required(),
                TextInput::make('nip')
                    ->label('NIP')
                    ->mask('9999.9999.999.9')
                    ->maxLength(15)
                    ->placeholder('ex. 3321.0299.201.9')
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
                Select::make('marital')
                    ->label('Status Perkawinan')
                    ->options(['Lajang' => 'Lajang', 'Menikah' => 'Menikah', 'Cerai Hidup' => 'Cerai Hidup', 'Cerai Mati' => 'Cerai Mati'])
                    ->required(),
                Textarea::make('address')
                    ->label('Alamat Lengkap')
                    ->placeholder('Jalan Chelsea, RT 20/RW 82, Kecamatan Liverpool, Kabupaten Manchester')
                    ->required(),
                TextInput::make('email')
                        ->label('Email Pribadi')
                        ->email()
                        ->placeholder('ex. tamam@gmail.com')
                        ->required(),
                TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel()
                    ->mask('9999-9999-9999')
                    ->placeholder('ex. 0812-3456-7890')
                    ->required(),
                TextInput::make('other_phone')
                    ->label('No. Telepon Kerabat')
                    ->tel()
                    ->mask('9999-9999-9999')
                    ->placeholder('ex. 0812-3456-7890')
                    ->required(),
                Select::make('other_phone_adverb')
                    ->label('Hubungan dengan Kerabat')
                    ->options(['Suami' => 'Suami', 'Istri' => 'Istri', 'Orang tua' =>  'Orang tua', 'Wali' => 'Wali', 'Saudara' => 'Saudara', 'Lainnya' => 'Lainnya'])
                    ->required(),
                DatePicker::make('entry_date')
                    ->label('Tanggal Masuk Kerja')
                    ->maxDate(now())
                    ->required(),
                DatePicker::make('retirement_date')
                    ->label('Tanggal Pensiun')
                    ->minDate(now())
                    ->required(),
                Select::make('staff_status_id')
                    ->label('Status Kepegawaian')
                    ->relationship('staffStatus', 'name')
                    ->reactive()
                    ->required(),
                Select::make('chair_id')
                    ->label('Jabatan')
                    ->relationship('chair', 'name')
                    ->required(),
                Select::make('group_id')
                    ->label('Kelompok Tenaga Kerja')
                    ->relationship('group', 'name')
                    ->required(),
                Select::make('unit_id')
                    ->label('Unit Kerja')
                    ->relationship('unit', 'name')
                    ->required(),
                Fieldset::make('Data Kontrak')
                    ->visible(fn (Get $get) => ($get('staff_status_id') ?? null) == 2)
                    ->schema([
                        TextInput::make('contract.contract_number')
                            ->label('Nomor Kontrak')
                            ->placeholder('ext. 123/12/KK/YMP-U/XI/2025')
                            ->required(),
                        DatePicker::make('contract.start_date')
                            ->label('Tanggal Mulai')
                            ->maxDate(now())
                            ->required(),
                        DatePicker::make('contract.end_date')
                            ->label('Tanggal Berakhir')
                            ->minDate(now())
                            ->required(),
                    ])
                    ->columnSpanFull(),

                Fieldset::make('Data Pengangkatan Pegawai')
                    ->visible(fn (Get $get) => ($get('staff_status_id') ?? null) == 1) 
                    ->schema([
                        TextInput::make('appointment.decree_number')
                            ->label('Nomor SK')
                            ->placeholder('ext. 12/12/SK/YMP/XI/2025
')
                            ->required(),
                        DatePicker::make('appointment.decree_date')
                            ->label('Tanggal SK')
                            ->required(),
                        TextInput::make('appointment.class')
                            ->label('Golongan')
                            ->placeholder('IIIa, IVb, dst.')
                            ->required(),
                    ])
                    ->columnSpanFull(),

                Fieldset::make('Data Penyesuaian Pegawai')
                    ->visible(fn (Get $get) => ($get('staff_status_id') ?? null) == 1)
                    ->schema([
                        TextInput::make('adjustment.decree_number')
                            ->label('Nomor SK Penyesuaian')
                            ->placeholder('ext. 21/02/SK/YMP/I/2016
'),
                        DatePicker::make('adjustment.decree_date')
                            ->label('Tanggal SK Penyesuaian'),
                        TextInput::make('adjustment.class')
                            ->label('Golongan Baru')
                            ->placeholder('IIIa, IVb, dst.'),
                    ])
                    ->columnSpanFull(),
                // === FORM TAMBAHAN DEPENDENSI ===
                Section::make('Data Tambahan Kepegawaian')
                    ->schema([
                        Fieldset::make('Pendidikan Awal')
                            ->schema([
                                Select::make('entryEducation.level')
                                    ->label('Jenjang')
                                    ->options(['Dokter' => 'Dokter', 'Dokter Gigi' => 'Dokter Gigi','Spesialis' => 'Spesialis', 'S2' => 'S2', 'S1' => 'S1', 'Profesi Ners' => 'Profesi Ners', 'Profesi Apoteker' => 'Profesi Apoteker', 'DIV' => 'DIV', 'DIII' => 'DIII', 'DIII Anestesi' => 'DIII Anestesi', 'DIV Anestesi' => 'DIV Anestesi', 'SMK' => 'SMK', 'SMA' => 'SMA', 'SMP' => 'SMP'
                                    ])
                                    ->required(),
                                TextInput::make('entryEducation.institution')
                                    ->label('Institusi')
                                    ->placeholder('ext. Universitas Mitra Paramedika')
                                    ->required(),
                                TextInput::make('entryEducation.certificate_number')
                                    ->label('Nomor Ijazah')
                                    ->placeholder('ext. 1234/ABC/ABCDE/KM/S-1/XI/25')
                                    ->required(),
                                DatePicker::make('entryEducation.certificate_date')
                                    ->label('Tanggal Ijazah')
                                    ->required(),
                                TextInput::make('entryEducation.nonformal_education')
                                    ->label('Pendidikan Nonformal')
                                    ->placeholder('ext. Kursus Mitra Paramedika'),
                                TextInput::make('entryEducation.adverb')
                                    ->label('Keterangan'),
                            ])
                            ->visible(),

                        // --- 5. WORK EDUCATION ---
                        Checkbox::make('has_work_education')
                            ->label('Memiliki Riwayat Pendidikan saat Bekerja?')
                            ->reactive(),
                        Fieldset::make('Pendidikan Kerja')
                            ->schema([
                                Select::make('workEducation.level')
                                    ->label('Jenjang')
                                    ->options(['Dokter' => 'Dokter', 'Dokter Gigi' => 'Dokter Gigi','Spesialis' => 'Spesialis', 'S2' => 'S2', 'S1' => 'S1', 'Profesi Ners' => 'Profesi Ners', 'Profesi Apoteker' => 'Profesi Apoteker', 'DIV' => 'DIV', 'DIII' => 'DIII', 'DIII Anestesi' => 'DIII Anestesi', 'DIV Anestesi' => 'DIV Anestesi', 'SMK' => 'SMK', 'SMA' => 'SMA', 'SMP' => 'SMP'
                                    ]),
                                TextInput::make('workEducation.major')
                                    ->label('Jurusan')
                                    ->placeholder('ext. Keperawatan'),
                                TextInput::make('workEducation.institution')
                                    ->label('Institusi')
                                    ->placeholder('ext. Universitas Mitra Paramedika'),
                                TextInput::make('workEducation.certificate_number')
                                    ->label('Nomor Ijazah')
                                    ->placeholder('1234/ABC/ABCDE/KM/S-1/IV/25'),
                                DatePicker::make('workEducation.certificate_date')
                                    ->label('Tanggal Ijazah'),
                            ])
                            ->visible(fn (Get $get) => $get('has_work_education')),

                        // --- 6. WORK EXPERIENCE ---
                        Checkbox::make('has_work_experience')
                            ->label('Memiliki Pengalaman Kerja Sebelumnya?')
                            ->reactive(),
                        Fieldset::make('Pengalaman Kerja')
                            ->schema([
                                TextInput::make('workExperience.institution')
                                    ->label('Instansi')
                                    ->placeholder('ext. RSU Mitra Paramedika'),
                                TextInput::make('workExperience.work_length')
                                    ->label('Lama Kerja')
                                    ->placeholder('ext. 2 Tahun'),
                                TextInput::make('workExperience.admission')
                                    ->label('Pengakuan'),
                            ])
                            ->visible(fn (Get $get) => $get('has_work_experience')),
                    ])
                    ->columnSpanFull(),
                    Checkbox::make('confirmation')
                    ->label('Buat akun pengguna untuk karyawan ini')
                    ->default(true)
                    ->visible(fn (string $context): bool => $context === 'create'),
            ]);
    }
}
