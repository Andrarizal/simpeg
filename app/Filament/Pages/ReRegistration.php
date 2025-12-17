<?php

namespace App\Filament\Pages;

use App\Models\Chair;
use App\Models\Group;
use App\Models\PreStaff;
use App\Models\Staff;
use App\Models\StaffEntryEducation;
use App\Models\StaffStatus;
use App\Models\StaffWorkEducation;
use App\Models\StaffWorkExperience;
use App\Models\Unit;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Http\Middleware\Authenticate;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;

class ReRegistration extends Page implements HasSchemas
{
    public array $data = [];

    protected static string|BackedEnum|null $navigationIcon = null; 
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.re-registration';
    protected static ?string $slug = 're-regist';
    protected static ?string $title = 'ReRegistration | SIMANTAP';

    public function mount(): void
    {
        // A. Ambil token dari URL ( ?token=... )
        $token = request()->query('token');

        // B. Cek apakah token ada?
        if (!$token) {
            abort(404, 'Token tidak ditemukan.');
        }

        // C. Cari data user berdasarkan token
        $preStaff = PreStaff::where('token', $token)->first();

        // D. Validasi jika data tidak ditemukan / token salah
        if (!$preStaff) {
            Notification::make()->title('Token Invalid')->danger()->send();
            redirect('/');
        }

        // E. ISI FORM SECARA OTOMATIS (AUTO-FILL)
        // Kita isi wadah $data dengan data dari database
        $this->form->fill([
            'token' => $token,
            'nik' => $preStaff->nik,
            'nip' => $preStaff->nip,
            'name' => $preStaff->name,
            'email' => $preStaff->email,
            'phone' => $preStaff->phone,
            'birth_date' => $preStaff->birth_date,
            'retirement_date' => Carbon::parse($preStaff->birth_date)->addYear(56)->format('Y-m-d'),
            'staff_status_id' => $preStaff->staff_status_id,
            'chair_id' => $preStaff->chair_id,
            'group_id' => $preStaff->group_id,
            'unit_id' => $preStaff->unit_id,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                ->schema([
                    Grid::make()
                        ->columns(1)
                        ->columnSpan(1)
                        ->schema([
                            Section::make('Profil Pegawai')
                                ->schema([
                                    Hidden::make('token'),
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
                                    TextInput::make('nik')
                                        ->label('NIK')
                                        ->disabled()
                                        ->dehydrated(),
                                    TextInput::make('name')
                                        ->label('Nama')
                                        ->placeholder('ex. Tamam Muhammad')
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
                                    TextInput::make('password')
                                        ->label('Password')
                                        ->password()
                                        ->revealable()
                                        ->minLength(6)
                                        ->required(),
                                ]),
                        ]),
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
                                            ToggleButtons::make('sex')
                                                ->label('Jenis Kelamin')
                                                ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                                                ->inline()
                                                ->required(),
                                            Select::make('marital')
                                                ->label('Status Perkawinan')
                                                ->options(['Lajang' => 'Lajang', 'Menikah' => 'Menikah', 'Cerai Hidup' => 'Cerai Hidup', 'Cerai Mati' => 'Cerai Mati'])
                                                ->required()
                                                ->native(false),
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
                                        ])
                                        ->columns(2),
                                    Tab::make('Data Pekerjaan')
                                        ->icon('heroicon-o-puzzle-piece')
                                        ->schema([
                                            TextInput::make('nip')
                                                ->label('NIP')
                                                ->mask('9999.9999.999.9')
                                                ->disabled()
                                                ->dehydrated(),
                                            DatePicker::make('entry_date')
                                                ->label('Terhitung Mulai Tanggal')
                                                ->maxDate(now())
                                                ->required()
                                                ->native(false),
                                            DatePicker::make('retirement_date')
                                                ->label('Tanggal Pensiun')
                                                ->minDate(now())
                                                ->disabled()
                                                ->dehydrated(),
                                            Select::make('staff_status_id')
                                                ->label('Status Kepegawaian')
                                                ->options(StaffStatus::pluck('name', 'id'))
                                                ->disabled()
                                                ->dehydrated(),
                                            Select::make('chair_id')
                                                ->label('Jabatan')
                                                ->options(Chair::pluck('name', 'id'))
                                                ->disabled()
                                                ->dehydrated(),
                                            Select::make('group_id')
                                                ->label('Kelompok Tenaga Kerja')
                                                ->options(Group::pluck('name', 'id'))
                                                ->disabled()
                                                ->dehydrated(),
                                            Select::make('unit_id')
                                                ->label('Unit Kerja')
                                                ->options(Unit::pluck('name', 'id'))
                                                ->disabled()
                                                ->dehydrated(),
                                        ])
                                        ->columns(2),

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
                                ]),
                            Action::make('save')
                                ->label('Registrasi Ulang')
                                ->color('primary')
                                ->action(function() {
                                    // Panggil fungsi submit logic Anda
                                    $this->submit(); 
                                })
                                ->extraAttributes([
                                    'class' => 'w-full lg:w-auto float-right', 
                                ]),
                        ]),
                ])
                ->columns(3)
                ->columnSpanFull(),
            ])
            ->columns(2)
            ->statePath('data'); 
    }

    public function submit() {
        $data = $this->form->validate(); 
        $validated = $data['data'];

        $staff = Staff::create([
            'pas' => collect($validated['pas'])->first() ?? null,
            'nik' => $validated['nik'],
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'sex' => $validated['sex'],
            'birth_place' => $validated['birth_place'],
            'birth_date' => $validated['birth_date'],
            'marital' => $validated['marital'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'other_phone' => $validated['other_phone'],
            'other_phone_adverb' => $validated['other_phone_adverb'],
            'origin' => $validated['origin'],
            'domicile' => $validated['domicile'],
            'entry_date' => $validated['entry_date'],
            'retirement_date' => $validated['retirement_date'],
            'staff_status_id' => $validated['staff_status_id'],
            'chair_id' => $validated['chair_id'],
            'group_id' => $validated['group_id'],
            'unit_id' => $validated['unit_id'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => 2,
            'staff_id' => $staff->id
        ]);

        if (!empty($validated['entryEducation']['level'])) {
            StaffEntryEducation::create([
                'staff_id' => $staff->id,
                'level' => $validated['entryEducation']['level'],
                'institution' => $validated['entryEducation']['institution'] ?? null,
                'certificate_number' => $validated['entryEducation']['certificate_number'] ?? null,
                'certificate_date' => $validated['entryEducation']['certificate_date'] ?? null,
                'certificate' => collect($validated['entryEducation']['certificate'])->first() ?? null,
                'nonformal_education' => $validated['entryEducation']['nonformal_education'] ?? null,
                'adverb' => $validated['entryEducation']['adverb']?? null,
            ]);
        }

        if (!empty($validated['workEducation']['level'])) {
            StaffWorkEducation::create([
                'staff_id' => $staff->id,
                'level' => $validated['workEducation']['level'],
                'major' => $validated['workEducation']['major'] ?? null,
                'institution' => $validated['workEducation']['institution'] ?? null,
                'certificate_number' => $validated['workEducation']['certificate_number'] ?? null,
                'certificate_date' => $validated['workEducation']['certificate_date'] ?? null,
                'certificate' => collect($validated['workEducation']['certificate'])->first() ?? null,
            ]);
        }

        if (!empty($validated['workExperience']['institution'])) {
            StaffWorkExperience::create([
                'staff_id' => $staff->id,
                'institution' => $validated['workExperience']['institution'],
                'work_length' => $validated['workExperience']['work_length'] ?? null,
                'certificate' => collect($validated['workExperience']['certificate'])->first() ?? null,
                'admission' => $validated['workExperience']['admission'] ?? null,
            ]);
        }

        PreStaff::where('token', $validated['token'])->delete();

        Notification::make()
            ->title('Berhasil Registrasi Ulang. Silahkan login menggunakan email dan password yang telah dibuat!')
            ->success()
            ->send();

        return redirect('/login');
    }

    // Layout & Middleware methods tetap sama...
    public function getLayout(): string
    {
        return 'filament.pages.regLayout';
    }

    public static function getWithoutRouteMiddleware(Panel $panel): string|array
    {
        return [Authenticate::class];
    }
    
    protected function getLayoutData(): array
    {
       return [
           'pageTitle' => static::$title,
       ];
    }
}
