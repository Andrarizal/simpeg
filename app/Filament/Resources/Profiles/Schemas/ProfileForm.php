<?php

namespace App\Filament\Resources\Profiles\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                ->schema([
                    // ============================
                    // 📍 Kolom Kiri (1/3)
                    // ============================
                    Grid::make()
                        ->columns(1)
                        ->columnSpan(1)
                        ->schema([
                            Section::make('Profil Pegawai')
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
                                        ->extraAttributes([
                                            'class' => 'mx-auto mb-2',
                                        ]),
                                    TextEntry::make('name')
                                        ->hiddenLabel()
                                        ->alignCenter()
                                        ->extraAttributes([
                                            'class' => 'text-center font-bold text-2xl -mt-4', // bold, besar, mepet foto
                                        ]),
                                    TextEntry::make('nip')
                                        ->hiddenLabel()
                                        ->alignCenter()
                                        ->extraAttributes([
                                            'class' => 'text-center text-sm text-gray-500 -mt-6', // kecil, abu-abu, mepet ke name
                                        ]),
                                    TextEntry::make('chair.name')
                                        ->label('Jabatan'),
                                    TextEntry::make('unit.name')
                                        ->label('Unit Kerja'),
                                    TextEntry::make('staffStatus.name')
                                        ->label('Status Kepegawaian'),
                                    TextEntry::make('group.name')
                                        ->label('Kelompok Tenaga Kerja'),
                                ]),
                        ]),

                    // ============================
                    // 📍 Kolom Kanan (2/3)
                    // ============================
                    Grid::make()
                        ->columns(1)
                        ->columnSpan(2)
                        ->schema([
                            Tabs::make('Informasi Pegawai')
                                ->tabs([
                                    // --- TAB DATA DIRI ---
                                    Tab::make('Data Diri')
                                        ->icon('heroicon-o-identification')
                                        ->schema([
                                            Section::make('Data Pribadi')
                                                ->columns(2)
                                                ->schema([
                                                    TextInput::make('nik')
                                                        ->label('NIK')
                                                        ->placeholder('ex. 3321029920192099')
                                                        ->maxLength(16)
                                                        ->required(),
                                                    ToggleButtons::make('sex')
                                                        ->label('Jenis Kelamin')
                                                        ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                                                        ->inline()
                                                        ->required(),
                                                    TextInput::make('birth_place')
                                                        ->label('Tempat Lahir')
                                                        ->placeholder('ex. Sleman')
                                                        ->required(),
                                                    DatePicker::make('birth_date')
                                                        ->label('Tanggal Lahir')
                                                        ->required()
                                                        ->reactive()
                                                        ->native(false),
                                                    Textarea::make('origin')
                                                        ->label('Alamat Asli')
                                                        ->rows(3)
                                                        ->placeholder('Jalan Chelsea, RT 20/RW 82, Kecamatan Liverpool, Kabupaten Manchester')
                                                        ->required(),
                                                    Textarea::make('domicile')
                                                        ->label('Alamat Domisili')
                                                        ->rows(3)
                                                        ->placeholder('Jalan Chelsea, RT 20/RW 82, Kecamatan Liverpool, Kabupaten Manchester')
                                                        ->required(),
                                                    Select::make('marital')
                                                        ->label('Status Perkawinan')
                                                        ->options(['Lajang' => 'Lajang', 'Menikah' => 'Menikah', 'Cerai Hidup' => 'Cerai Hidup', 'Cerai Mati' => 'Cerai Mati'])
                                                        ->required()
                                                        ->native(false),
                                                    DatePicker::make('entry_date')
                                                        ->label('Terhitung Mulai Tanggal')
                                                        ->maxDate(now())
                                                        ->required()
                                                        ->native(false),
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
                                                        ->required()
                                                        ->native(false),
                                                ]),
                                        ]),

                                    // --- TAB PENDIDIKAN ---
                                    Tab::make('Riwayat Pendidikan')
                                        ->icon('heroicon-o-academic-cap')
                                        ->schema([
                                            Section::make('Data Pendidikan Sebelum Bekerja')
                                                ->columns(2)
                                                ->schema([
                                                    Select::make('entryEducation.level')
                                                    ->label('Jenjang')
                                                    ->options(['Dokter' => 'Dokter', 'Dokter Gigi' => 'Dokter Gigi','Spesialis' => 'Spesialis', 'S2' => 'S2', 'S1' => 'S1', 'Profesi Ners' => 'Profesi Ners', 'Profesi Apoteker' => 'Profesi Apoteker', 'DIV' => 'DIV', 'DIII' => 'DIII', 'DIII Anestesi' => 'DIII Anestesi', 'DIV Anestesi' => 'DIV Anestesi', 'SMK' => 'SMK', 'SMA' => 'SMA', 'SMP' => 'SMP'
                                                    ])
                                                    ->required()
                                                    ->native(false),
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
                                                    ->required()
                                                    ->native(false),
                                                FileUpload::make('entryEducation.certificate')
                                                    ->label('Ijazah')
                                                    ->disk('public')
                                                    ->visibility('public')
                                                    ->directory('ijazah-awal')
                                                    ->required()
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->maxSize(2048) // maksimal 2MB
                                                    ->helperText('Unggah ijazah dalam format PDF')
                                                    ->columnSpanFull(),
                                                TextInput::make('entryEducation.nonformal_education')
                                                    ->label('Pendidikan Nonformal')
                                                    ->placeholder('ext. Kursus Mitra Paramedika'),
                                                TextInput::make('entryEducation.adverb')
                                                    ->label('Keterangan'),
                                                ]),
                                            Section::make('Data Pendidikan Saat Bekerja')
                                                ->description('Masukkan riwayat pendidikan terakhir atau seluruhnya.')
                                                ->columns(2)
                                                ->schema([
                                                    Select::make('workEducation.level')
                                                        ->label('Jenjang')
                                                        ->options(['Dokter' => 'Dokter', 'Dokter Gigi' => 'Dokter Gigi','Spesialis' => 'Spesialis', 'S2' => 'S2', 'S1' => 'S1', 'Profesi Ners' => 'Profesi Ners', 'Profesi Apoteker' => 'Profesi Apoteker', 'DIV' => 'DIV', 'DIII' => 'DIII', 'DIII Anestesi' => 'DIII Anestesi', 'DIV Anestesi' => 'DIV Anestesi', 'SMK' => 'SMK', 'SMA' => 'SMA', 'SMP' => 'SMP'
                                                        ])
                                                        ->native(false),
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
                                                        ->label('Tanggal Ijazah')
                                                        ->native(false),
                                                    FileUpload::make('workEducation.certificate')
                                                        ->label('Ijazah')
                                                        ->disk('public')
                                                        ->visibility('public')
                                                        ->directory('ijazah-bekerja')
                                                        ->acceptedFileTypes(['application/pdf'])
                                                        ->maxSize(2048) // maksimal 2MB
                                                        ->helperText('Unggah ijazah dalam format PDF'),
                                                ]),
                                        ]),

                                    // --- TAB PENGALAMAN ---
                                    Tab::make('Riwayat Pengalaman')
                                        ->icon('heroicon-o-briefcase')
                                        ->schema([
                                            Section::make('Data Pengalaman')
                                                ->columns(2)
                                                ->schema([
                                                    TextInput::make('workExperience.institution')
                                                        ->label('Instansi')
                                                        ->placeholder('ext. RSU Mitra Paramedika'),
                                                    TextInput::make('workExperience.work_length')
                                                        ->label('Lama Kerja')
                                                        ->placeholder('ext. 2 Tahun'),
                                                    TextInput::make('workExperience.admission')
                                                        ->label('Pengakuan'),
                                                    FileUpload::make('workExperience.certificate')
                                                        ->label('Sertifikat')
                                                        ->disk('public')
                                                        ->visibility('public')
                                                        ->directory('ijazah')
                                                        ->acceptedFileTypes(['application/pdf'])
                                                        ->maxSize(2048) // maksimal 2MB
                                                        ->helperText('Unggah sertifikat dalam format PDF'),
                                                ]),
                                        ]),
                                        // --- TAB PENGALAMAN ---
                                    Tab::make('Riwayat Pelatihan')
                                        ->icon('heroicon-o-swatch')
                                        ->schema([
                                            TextEntry::make('training_alert')
                                                ->hiddenLabel()
                                                ->state(function (Get $get) {
                                                    // Ambil data dari repeater (raw array state)
                                                    $trainings = $get('training') ?? []; 
                                                    $totalDuration = 0;
                                                    $currentYear = now()->year;

                                                    foreach ($trainings as $item) {
                                                        $date = $item['training_date'] ?? null;
                                                        $duration = $item['duration'] ?? 0;

                                                        // Hitung hanya jika tanggal valid & tahun ini
                                                        if ($date && Carbon::parse($date)->year == $currentYear) {
                                                            $totalDuration += (float) $duration;
                                                        }
                                                    }

                                                    if ($totalDuration < 20) {
                                                        $kurang = 20 - $totalDuration;
                                                        
                                                        return new HtmlString("
                                                            <div class='flex items-center gap-3 p-4 text-xs text-yellow-800 bg-yellow-50 rounded-2xl dark:bg-yellow-900/30 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800'>
                                                                <svg class='w-5 h-5 shrink-0' fill='currentColor' viewBox='0 0 20 20'>
                                                                    <path fill-rule='evenodd' d='M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z' clip-rule='evenodd'></path>
                                                                </svg>
                                                                <div>
                                                                    <span class='font-bold'>Target Belum Tercapai!</span> 
                                                                    Total pelatihan tahun ini baru <strong>{$totalDuration} jam</strong>. 
                                                                    Anda kurang <strong>{$kurang} jam</strong> lagi untuk mencapai target 20 jam.
                                                                </div>
                                                            </div>
                                                        ");
                                                    }

                                                    // Jika sudah tercapai, bisa return null (sembunyi) atau pesan sukses
                                                    return new HtmlString("
                                                        <div class='flex items-center gap-3 p-4 text-xs text-green-800 bg-green-50 rounded-2xl dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800'>
                                                            <svg class='w-5 h-5 shrink-0' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'></path></svg>
                                                            <div>
                                                                <span class='font-bold'>Hebat!</span>Target 20 jam per tahun sudah terpenuhi ({$totalDuration} jam).
                                                            </div>
                                                        </div>
                                                    ");
                                                }),
                                            Repeater::make('training')
                                                ->hiddenLabel()
                                                ->live()
                                                ->relationship(modifyQueryUsing: fn (Builder $query) => $query->orderBy('training_date', 'desc'))
                                                ->schema([
                                                    Grid::make(2)
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->label('Nama Pelatihan')
                                                                ->required(),
                                                            DatePicker::make('training_date')
                                                                ->label('Tanggal Pelatihan')
                                                                ->maxDate(now())
                                                                ->required()
                                                                ->native(false),
                                                            TextInput::make('duration')
                                                                ->label('Durasi')
                                                                ->required()
                                                                ->numeric(),
                                                            TextInput::make('notes')
                                                                ->label('Keterangan'),
                                                            TextArea::make('description')
                                                                ->label('Deskripsi')
                                                                ->rows(3),
                                                            FileUpload::make('certificate')
                                                                ->label('Sertifikat')
                                                                ->disk('public')
                                                                ->visibility('public')
                                                                ->directory('pelatihan')
                                                                ->acceptedFileTypes(['application/pdf'])
                                                                ->maxSize(2048) // maksimal 2MB
                                                                ->helperText('Unggah sertifikat dalam format PDF'),
                                                        ]),
                                                ])
                                                ->addActionLabel('Tambah Pelatihan Baru')
                                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                                ->collapsed(false)
                                                ->deleteAction(
                                                    fn ($action) => $action->requiresConfirmation(),
                                                ),
                                        ]),
                                    Tab::make('Perjanjian Kontrak')
                                        ->icon('heroicon-o-clipboard-document-check')
                                        ->visible(fn (Get $get) => ($get('staff_status_id') ?? null) <= 2)
                                        ->schema([
                                            Section::make('Data Kontrak')
                                                ->columns(2)
                                                ->visible(fn (Get $get) => ($get('staff_status_id') ?? null) == 2)
                                                ->schema([
                                                    TextInput::make('contract.contract_number')
                                                        ->label('Nomor Kontrak')
                                                        ->placeholder('ext. 123/12/KK/YMP-U/XI/2025')
                                                        ->disabled()
                                                        ->dehydrated(),
                                                    DatePicker::make('contract.start_date')
                                                        ->label('Tanggal Mulai')
                                                        ->maxDate(now())
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->native(false),
                                                    DatePicker::make('contract.end_date')
                                                        ->label('Tanggal Berakhir')
                                                        ->minDate(now())
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->native(false),
                                                    FileUpload::make('contract.decree')
                                                        ->label('Surat Kontrak')
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->disk('public')
                                                        ->visibility('public')
                                                        ->directory('surat-kontrak')
                                                        ->acceptedFileTypes(['application/pdf'])
                                                        ->maxSize(2048) // maksimal 2MB
                                                        ->helperText('Unggah surat kontrak dalam format PDF'),
                                                ]),
                                            Section::make('Data Pengangkatan Pegawai Tetap')
                                                ->columns(2)
                                                ->visible(fn (Get $get) => ($get('staff_status_id') ?? null) == 1)
                                                ->schema([
                                                    TextInput::make('appointment.decree_number')
                                                        ->label('Nomor SK')
                                                        ->placeholder('ext. 12/12/SK/YMP/XI/2025')
                                                        ->disabled()
                                                        ->dehydrated(),
                                                    DatePicker::make('appointment.decree_date')
                                                        ->label('Tanggal SK')
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->native(false),
                                                    TextInput::make('appointment.class')
                                                        ->label('Golongan')
                                                        ->placeholder('IIIa, IVb, dst.')
                                                        ->disabled()
                                                        ->dehydrated(),
                                                    FileUpload::make('appointment.decree')
                                                        ->label('Surat Pengangkatan Pegawai Tetap')
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->disk('public')
                                                        ->visibility('public')
                                                        ->directory('surat-pengangkatan')
                                                        // ->required()
                                                        ->acceptedFileTypes(['application/pdf'])
                                                        ->maxSize(2048) // maksimal 2MB
                                                        ->helperText('Unggah surat pengangkatan pegawai tetap dalam format PDF'),
                                                ]),
                                            Section::make('Data Penyesuaian Golongan Pegawai Tetap')
                                                ->columns(2)
                                                ->visible(fn (Get $get) => ($get('staff_status_id') ?? null) == 1)
                                                ->schema([
                                                    TextInput::make('adjustment.decree_number')
                                                        ->label('Nomor SK Penyesuaian')
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->placeholder('ext. 21/02/SK/YMP/I/2016'),
                                                    DatePicker::make('adjustment.decree_date')
                                                        ->label('Tanggal SK Penyesuaian')
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->native(false),
                                                    TextInput::make('adjustment.class')
                                                        ->label('Golongan Baru')
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->placeholder('IIIa, IVb, dst.'),
                                                    FileUpload::make('adjustment.decree')
                                                        ->label('Surat Penyesuaian')
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->disk('public')
                                                        ->visibility('public')
                                                        ->directory('surat-penyesuaian')
                                                        // ->required()
                                                        ->acceptedFileTypes(['application/pdf'])
                                                        ->maxSize(2048) // maksimal 2MB
                                                        ->helperText('Unggah surat penyesuaian golongan dalam format PDF'),
                                                ]),
                                        ]),
                                ]),
                        ]),
                ])
                ->columns(3)
                ->columnSpanFull(),
        ]);
    }
}
