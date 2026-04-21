<?php

namespace App\Filament\Resources\Staff\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Livewire\WithFileUploads;

class StaffForm
{
    use WithFileUploads;
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                ->schema([
                    Group::make()
                        ->schema([
                            Section::make()
                                ->schema([
                                    FileUpload::make('pas')
                                        ->label('Foto Profil')
                                        ->image()
                                        ->imageEditor()
                                        ->directory('profile')
                                        ->maxSize(2048)
                                        ->avatar()
                                        ->circleCropper()
                                        ->columnSpanFull()
                                        ->alignCenter()
                                        ->hiddenLabel()
                                        ->extraAttributes(['class' => 'mx-auto mb-4']),

                                    TextInput::make('name')
                                        ->label('Nama Lengkap')
                                        ->placeholder('Nama sesuai KTP')
                                        ->required()
                                        ->columnSpanFull(),

                                    TextInput::make('nip')
                                        ->label('Nomor Induk Kepegawaian')
                                        ->mask('9999.9999.999.9')
                                        ->placeholder('ex. 3321.0299...')
                                        ->suffixIcon('heroicon-m-identification')
                                        ->extraAttributes([
                                            'class' => 'font-mono'
                                        ])
                                        ->required(),

                                    TextInput::make('nik')
                                        ->label('Nomor Induk Kependudukan')
                                        ->maxLength(16)
                                        ->placeholder('16 Digit NIK')
                                        ->suffixIcon('heroicon-m-credit-card')
                                        ->extraAttributes([
                                            'class' => 'font-mono'
                                        ])
                                        ->required(),

                                    ToggleButtons::make('sex')
                                        ->label('Jenis Kelamin')
                                        ->options([
                                            'L' => 'Laki-laki',
                                            'P' => 'Perempuan'
                                        ])
                                        ->icons([
                                            'L' => 'heroicon-o-user',
                                            'P' => 'heroicon-o-user',
                                        ])
                                        ->colors([
                                            'L' => 'info',
                                            'P' => 'danger',
                                        ])
                                        ->inline()
                                        ->extraAttributes([
                                            'class' => '[&_label]:text-xs'
                                        ])
                                        ->required(),

                                        DatePicker::make('birth_date')
                                        ->label('Tanggal Lahir')
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('d F Y')
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (callable $set, $state) {
                                            if ($state) {
                                                $set('retirement_date', Carbon::parse($state)->addYear(56)->format('Y-m-d'));
                                            }
                                        }),

                                    TextInput::make('birth_place')
                                        ->label('Tempat Lahir')
                                        ->placeholder('Kota Kelahiran')
                                        ->required(),

                                    Select::make('marital')
                                        ->label('Status Perkawinan')
                                        ->options([
                                            'Lajang' => 'Lajang',
                                            'Menikah' => 'Menikah',
                                            'Cerai Hidup' => 'Cerai Hidup',
                                            'Cerai Mati' => 'Cerai Mati'
                                        ])
                                        ->native(false)
                                        ->required(),
                                ])
                                ->compact(),
                        ])
                        ->columnSpan(['lg' => 1]),

                    Group::make()
                        ->schema([
                            Section::make('Kontak & Domisili')
                                ->icon('heroicon-m-map-pin')
                                ->collapsible()
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
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->prefixIcon('heroicon-m-envelope')
                                            ->required(),

                                        TextInput::make('phone')
                                            ->label('No. WhatsApp/HP')
                                            ->tel()
                                            ->mask('9999-9999-9999')
                                            ->prefixIcon('heroicon-m-phone')
                                            ->required(),
                                    ]),

                                    Grid::make(2)->schema([
                                        TextInput::make('other_phone')
                                            ->label('No. Telepon Kerabat')
                                            ->tel()
                                            ->mask('9999-9999-9999'),
                                        
                                        Select::make('other_phone_adverb')
                                            ->label('Hubungan')
                                            ->options(['Suami' => 'Suami', 'Istri' => 'Istri', 'Orang tua' => 'Orang tua', 'Wali' => 'Wali', 'Saudara' => 'Saudara', 'Lainnya' => 'Lainnya'])
                                            ->native(false)
                                            ->placeholder('None'),
                                    ]),

                                    Grid::make(2)->schema([
                                        Textarea::make('origin')
                                            ->label('Alamat KTP')
                                            ->rows(4)
                                            ->required(),
                                        
                                        Textarea::make('domicile')
                                            ->label('Alamat Domisili')
                                            ->rows(4)
                                            ->required(),
                                    ]),
                                ]),

                            Section::make('Data Kepegawaian')
                                ->icon('heroicon-m-briefcase')
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
                                        DatePicker::make('entry_date')
                                            ->label('Terhitung Mulai Tanggal')
                                            ->native(false)
                                            ->displayFormat('d F Y')
                                            ->required(),
                                        
                                        DatePicker::make('retirement_date')
                                            ->label('Perkiraan Pensiun')
                                            ->native(false)
                                            ->displayFormat('d F Y')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required(),
                                    ]),

                                    Grid::make(2)->schema([
                                        Select::make('staff_status_id')
                                            ->label('Status Pegawai')
                                            ->relationship('staffStatus', 'name')
                                            ->preload()
                                            ->native(false)
                                            ->live()
                                            ->required(),

                                        Select::make('group_id')
                                            ->label('Kelompok Tenaga')
                                            ->relationship('group', 'name')
                                            ->preload()
                                            ->native(false)
                                            ->live()
                                            ->required(),
                                    ]),

                                    Grid::make(2)->schema([
                                        Select::make('unit_id')
                                            ->label('Unit Kerja')
                                            ->relationship('unit', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->live()
                                            ->required(),

                                        Select::make('chair_id')
                                            ->label('Jabatan Struktural')
                                            ->relationship('chair', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->live()
                                            ->required(),
                                    ]),
                                ]),
                        ])
                        ->columnSpan(['lg' => 2]), 
                ])
                ->columnSpanFull(),
                Fieldset::make('Data Kontrak')
                    ->visible(fn (Get $get) => ($get('staff_status_id') ?? null) == 2)
                    ->schema([
                        TextInput::make('contract.contract_number')
                            ->label('Nomor Kontrak')
                            ->placeholder('cth. 123/12/KK/YMP-U/XI/2025')
                            ->live(onBlur: true)
                            ->required(),
                        DatePicker::make('contract.start_date')
                            ->label('Tanggal Mulai')
                            ->maxDate(fn () => Carbon::today())
                            ->required()
                            ->native(false),
                        DatePicker::make('contract.end_date')
                            ->label('Tanggal Berakhir')
                            ->minDate(fn () => Carbon::today())
                            ->required()
                            ->native(false),
                        FileUpload::make('contract.decree')
                            ->label('Surat Kontrak')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('surat-kontrak')
                            ->required()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->helperText('Unggah surat kontrak dalam format PDF'),
                    ])
                    ->columnSpanFull(),

                Fieldset::make('Data Pengangkatan Pegawai')
                    ->visible(fn (Get $get) => ($get('staff_status_id') ?? null) == 1) 
                    ->schema([
                        TextInput::make('appointment.decree_number')
                            ->label('Nomor SK')
                            ->placeholder('cth. 12/12/SK/YMP/XI/2025')
                            ->live(onBlur: true)
                            ->required(),
                        DatePicker::make('appointment.decree_date')
                            ->label('Tanggal SK')
                            ->required()
                            ->native(false),
                        TextInput::make('appointment.class')
                            ->label('Golongan')
                            ->placeholder('IIIa, IVb, dst.')
                            ->required(),
                        FileUpload::make('appointment.decree')
                            ->label('Surat Pengangkatan Pegawai Tetap')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('surat-pengangkatan')
                            ->required()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->helperText('Unggah surat pengangkatan pegawai tetap dalam format PDF'),
                    ])
                    ->columnSpanFull(),

                Fieldset::make('Data Penyesuaian Pegawai')
                    ->visible(fn (Get $get) => ($get('staff_status_id') ?? null) == 1)
                    ->schema([
                        TextInput::make('adjustment.decree_number')
                            ->label('Nomor SK Penyesuaian')
                            ->live(onBlur: true)
                            ->placeholder('cth. 21/02/SK/YMP/I/2016'),
                        DatePicker::make('adjustment.decree_date')
                            ->label('Tanggal SK Penyesuaian')
                            ->native(false),
                        TextInput::make('adjustment.class')
                            ->label('Golongan Baru')
                            ->placeholder('IIIa, IVb, dst.'),
                        FileUpload::make('adjustment.decree')
                            ->label('Surat Penyesuaian')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('surat-penyesuaian')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->helperText('Unggah surat penyesuaian golongan dalam format PDF'),
                    ])
                    ->columnSpanFull(),
                Section::make('Data Tambahan Kepegawaian')
                    ->schema([
                        Fieldset::make('Pendidikan Awal')
                            ->schema([
                                Select::make('entryEducation.level')
                                    ->label('Jenjang')
                                    ->options(['Dokter' => 'Dokter', 'Dokter Gigi' => 'Dokter Gigi','Spesialis' => 'Spesialis', 'S2' => 'S2', 'S1' => 'S1', 'Profesi Ners' => 'Profesi Ners', 'Profesi Apoteker' => 'Profesi Apoteker', 'DIV' => 'DIV', 'DIII' => 'DIII', 'DIII Anestesi' => 'DIII Anestesi', 'DIV Anestesi' => 'DIV Anestesi', 'SMK' => 'SMK', 'SMA' => 'SMA', 'SMP' => 'SMP'
                                    ])
                                    ->required()
                                    ->native(false),
                                TextInput::make('entryEducation.institution')
                                    ->label('Institusi')
                                    ->placeholder('cth. Universitas Mitra Paramedika')
                                    ->required(),
                                TextInput::make('entryEducation.certificate_number')
                                    ->label('Nomor Ijazah')
                                    ->placeholder('cth. 1234/ABC/ABCDE/KM/S-1/XI/25')
                                    ->required(),
                                DatePicker::make('entryEducation.certificate_date')
                                    ->label('Tanggal Ijazah')
                                    ->required()
                                    ->native(false),
                                FileUpload::make('entryEducation.certificate')
                                    ->label('Ijazah')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('ijazah-awal')
                                    ->required()
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(2048)
                                    ->helperText('Unggah ijazah dalam format PDF')
                                    ->columnSpanFull(),
                                TextInput::make('entryEducation.nonformal_education')
                                    ->label('Pendidikan Nonformal')
                                    ->placeholder('cth. Kursus Mitra Paramedika'),
                                TextInput::make('entryEducation.adverb')
                                    ->label('Keterangan'),
                            ])
                            ->visible(),

                        Checkbox::make('has_work_education')
                            ->label('Memiliki Riwayat Pendidikan saat Bekerja?')
                            ->reactive(),
                        Fieldset::make('Pendidikan Kerja')
                            ->schema([
                                Select::make('workEducation.level')
                                    ->label('Jenjang')
                                    ->options(['Dokter' => 'Dokter', 'Dokter Gigi' => 'Dokter Gigi','Spesialis' => 'Spesialis', 'S2' => 'S2', 'S1' => 'S1', 'Profesi Ners' => 'Profesi Ners', 'Profesi Apoteker' => 'Profesi Apoteker', 'DIV' => 'DIV', 'DIII' => 'DIII', 'DIII Anestesi' => 'DIII Anestesi', 'DIV Anestesi' => 'DIV Anestesi', 'SMK' => 'SMK', 'SMA' => 'SMA', 'SMP' => 'SMP'
                                    ])
                                    ->placeholder('None')
                                    ->native(false),
                                TextInput::make('workEducation.major')
                                    ->label('Jurusan')
                                    ->placeholder('cth. Keperawatan'),
                                TextInput::make('workEducation.institution')
                                    ->label('Institusi')
                                    ->placeholder('cth. Universitas Mitra Paramedika'),
                                TextInput::make('workEducation.certificate_number')
                                    ->label('Nomor Ijazah')
                                    ->placeholder('1234/ABC/ABCDE/KM/S-1/IV/25'),
                                DatePicker::make('workEducation.certificate_date')
                                    ->label('Tanggal Ijazah')
                                    ->native(false),
                                FileUpload::make('workEducation.certificate')
                                    ->label('Ijazah')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('ijazah-bekerja')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(2048)
                                    ->helperText('Unggah ijazah dalam format PDF'),
                            ])
                            ->visible(fn (Get $get) => $get('has_work_education')),

                        Checkbox::make('has_work_experience')
                            ->label('Memiliki Pengalaman Kerja Sebelumnya?')
                            ->reactive(),
                        Fieldset::make('Pengalaman Kerja')
                            ->schema([
                                TextInput::make('workExperience.institution')
                                    ->label('Instansi')
                                    ->placeholder('cth. RSU Mitra Paramedika'),
                                TextInput::make('workExperience.work_length')
                                    ->label('Lama Kerja')
                                    ->placeholder('cth. 2 Tahun'),
                                TextInput::make('workExperience.admission')
                                    ->label('Pengakuan'),
                                FileUpload::make('workExperience.certificate')
                                    ->label('Sertifikat')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('sertifikat')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(2048)
                                    ->helperText('Unggah sertifikat dalam format PDF'),
                            ])
                            ->visible(fn (Get $get) => $get('has_work_experience')),
                    ])
                    ->columnSpanFull(),
                    Checkbox::make('confirmation')
                        ->label('Buat akun pengguna untuk karyawan ini')
                        ->default(true)
                        ->visible(fn (string $context): bool => $context == 'create'),
            ]);
    }
}
