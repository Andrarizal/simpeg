<?php

namespace App\Filament\Resources\Staff\Schemas;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(['default' => 1, 'md' => 3])
                ->schema([
                    Grid::make()
                        ->columns(1)
                        ->columnSpan(1)
                        ->schema([
                            Section::make()
                                ->schema([
                                    TextEntry::make('pas')
                                        ->hiddenLabel()
                                        ->html()
                                        ->extraAttributes(['class' => 'flex justify-center'])
                                        ->state(function ($record) {
                                            $imageUrl = null;

                                            if ($record->pas && asset('storage/' . $record->pas)) {
                                                $imageUrl = asset('storage/' . $record->pas);
                                            }

                                            $initials = collect(explode(' ', $record->name))
                                                ->map(fn ($segment) => $segment[0] ?? '')
                                                ->take(2)
                                                ->join('');

                                            if ($imageUrl) {
                                                return '
                                                    <div class="shrink-0 relative">
                                                        <img src="' . $imageUrl . '" 
                                                            alt="' . $record->name . '" 
                                                            class="w-28 h-28 rounded-full object-cover border-4 border-white/20 shadow-md bg-gray-200">
                                                    </div>
                                                ';
                                            }

                                            return '
                                                <div class="shrink-0 relative">
                                                    <div class="w-28 h-28 rounded-full bg-gray-500 flex items-center justify-center text-3xl font-bold text-white border-4 border-white/10 shadow-md">
                                                        ' . strtoupper($initials) . '
                                                    </div>
                                                </div>
                                            ';
                                        }),
                                    TextEntry::make('name')
                                        ->hiddenLabel()
                                        ->alignCenter()
                                        ->extraAttributes([
                                            'class' => 'text-center font-bold text-xl -mt-3',
                                            'style' => 'line-height: 1em'
                                        ]),
                                    TextEntry::make('nip')
                                        ->hiddenLabel()
                                        ->alignCenter()
                                        ->extraAttributes([
                                            'class' => 'text-center text-sm text-gray-500 font-mono -mt-6',
                                        ]),
                                ])
                                ->columns(1),
                        ]),
                    Grid::make()
                        ->columns(1)
                        ->columnSpan(2)
                        ->schema([
                            Section::make('Profil Pegawai')
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
                                    TextEntry::make('chair.name')
                                        ->label('Jabatan')
                                        ->extraAttributes([
                                            'class' => '-mt-2'
                                        ]),
                                    TextEntry::make('unit.name')
                                        ->label('Unit Kerja')
                                        ->extraAttributes([
                                            'class' => '-mt-2'
                                        ]),
                                    TextEntry::make('staffStatus.name')
                                        ->label('Status Kepegawaian')
                                        ->extraAttributes([
                                            'class' => '-mt-2'
                                        ]),
                                    TextEntry::make('group.name')
                                        ->label('Kelompok Tenaga Kerja')
                                        ->extraAttributes([
                                            'class' => '-mt-2'
                                        ]),
                                ])
                                ->columns(2),
                        ]),
                ]),

            // --- LAYOUT UTAMA (Grid 3 Kolom) ---
            Grid::make(['default' => 1, 'md' => 3])
                ->schema([
                    Group::make([
                        Section::make('Informasi Pribadi')
                            ->columnSpan(['md' => 2])
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
                            ->columns(2)
                            ->schema([
                                TextEntry::make('nik')
                                    ->label('Nomor Induk Kependudukan')
                                    ->columnSpanFull()
                                    ->extraAttributes([
                                        'class' => 'font-bold text-4xl -mt-2 font-mono',
                                    ]),
                                TextEntry::make('birth_place')
                                    ->label('Tempat Lahir')
                                    ->extraAttributes([
                                            'class' => '-mt-2'
                                    ]                                    ),
                                TextEntry::make('birth_date')
                                    ->label('Tanggal Lahir')
                                    ->extraAttributes([
                                            'class' => '-mt-2'
                                    ]                                    )->date(),
                                TextEntry::make('sex')
                                    ->label('Jenis Kelamin')
                                    ->extraAttributes([
                                            'class' => '-mt-2'
                                    ]),
                                TextEntry::make('marital')
                                    ->label('Status Perkawinan')
                                    ->extraAttributes([
                                            'class' => '-mt-2'
                                    ]),
                                TextEntry::make('origin')
                                    ->label('Alamat KTP')
                                    ->extraAttributes([
                                            'class' => '-mt-2'
                                    ])->columnSpanFull(),
                                TextEntry::make('domicile')
                                    ->label('Alamat Domisili')
                                    ->extraAttributes([
                                            'class' => '-mt-2'
                                    ])->columnSpanFull(),
                            ]),

                        Tabs::make('Data Kepegawaian')
                            ->columnSpan(['md' => 2])
                            ->tabs([
                                Tab::make('Kontrak & SK')
                                    ->icon('heroicon-m-document-text')
                                    ->visible(fn ($record) => $record->contract || $record->appointment || $record->adjustment)
                                    ->schema([
                                        Section::make('Kontrak Terakhir')
                                            ->visible(fn ($record) => $record->contract)
                                            ->columns(3)
                                            ->schema([
                                                TextEntry::make('contract.contract_number')
                                                    ->label('No. Kontrak'),
                                                TextEntry::make('contract.start_date')
                                                    ->label('Mulai')
                                                    ->date(),
                                                TextEntry::make('contract.end_date')
                                                    ->label('Selesai')
                                                    ->date(),
                                                self::getPdfEntry('contract.decree', 'File Kontrak', 'contract')
                                                    ->columnSpanFull(),
                                            ]),
                                        Section::make('SK Pengangkatan')
                                            ->visible(fn ($record) => $record->appointment)
                                            ->columns(3)
                                            ->schema([
                                                TextEntry::make('appointment.decree_number')->label('No. SK'),
                                                TextEntry::make('appointment.decree_date')->label('Tgl SK')->date(),
                                                TextEntry::make('appointment.class')->label('Golongan'),
                                                self::getPdfEntry('appointment.decree', 'File SK', 'appointment')->columnSpanFull(),
                                            ]),
                                        Section::make('Penyesuaian Golongan')
                                            ->visible(fn ($record) => $record->adjustment)
                                            ->columns(3)
                                            ->schema([
                                                TextEntry::make('adjustment.decree_number')->label('No. SK'),
                                                TextEntry::make('adjustment.decree_date')->label('Tgl SK')->date(),
                                                TextEntry::make('adjustment.class')->label('Golongan Baru'),
                                                self::getPdfEntry('adjustment.decree', 'File SK', 'adjustment')->columnSpanFull(),
                                            ]),
                                    ]),
                                
                                Tab::make('Pendidikan')
                                    ->icon('heroicon-m-academic-cap')
                                    ->schema([
                                        Section::make('Pendidikan Masuk')
                                            ->visible(fn ($record) => $record->entryEducation)
                                            ->columns(2)
                                            ->schema([
                                                TextEntry::make('entryEducation.level')->label('Jenjang'),
                                                TextEntry::make('entryEducation.institution')->label('Institusi'),
                                                TextEntry::make('entryEducation.certificate_number')->label('Nomor Ijazah'),
                                                TextEntry::make('entryEducation.certificate_date')->label('Tanggal Ijazah')->date(),
                                                self::getPdfEntry('entryEducation.certificate', 'File Ijazah', 'entry_education')->columnSpanFull(),
                                            ]),
                                        Section::make('Pendidikan Saat Bekerja')
                                            ->visible(fn ($record) => $record->workEducation)
                                            ->columns(3)
                                            ->schema([
                                                TextEntry::make('workEducation.level')->label('Jenjang'),
                                                TextEntry::make('workEducation.institution')->label('Institusi'),
                                                TextEntry::make('workEducation.major')->label('Program Studi'),
                                                TextEntry::make('workEducation.certificate_number')->label('Nomor Ijazah')->columnSpan(2),
                                                TextEntry::make('workEducation.certificate_date')->label('Tanggal Ijazah')->date(),
                                                self::getPdfEntry('workEducation.certificate', 'File Ijazah', 'work_education')->columnSpanFull(),
                                            ]),
                                    ]),
                                Tab::make('Pengalaman Kerja')
                                    ->icon('heroicon-o-briefcase')
                                    ->visible(fn ($record) => $record->workExperience)
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('workExperience.institution')->label('Institusi'),
                                        TextEntry::make('workExperience.work_length')->label('Lama Bekerja'),
                                        TextEntry::make('workExperience.admission')->label('Pengakuan'),
                                        self::getPdfEntry('workExperience.certificate', 'Sertifikat', 'work_experiences')->visible(fn($state) => $state ? true : false),
                                    ]),
                            ]),
                    ])->columnSpan(['md' => 2]),

                    Group::make([
                        Section::make('Kontak')
                            ->icon('heroicon-m-phone')
                            ->schema([
                                TextEntry::make('email')
                                    ->icon('heroicon-m-envelope')
                                    ->extraAttributes([
                                        'class' => '-mt-2'
                                    ])
                                    ->label('Email'),
                                TextEntry::make('phone')
                                    ->icon('heroicon-m-device-phone-mobile')
                                    ->extraAttributes([
                                        'class' => '-mt-2'
                                    ])
                                    ->label('No. HP'),
                                TextEntry::make('other_phone')
                                    ->icon('heroicon-m-device-phone-mobile')
                                    ->extraAttributes([
                                        'class' => '-mt-2'
                                    ])
                                    ->label('No. HP Kerabat')
                                    ->formatStateUsing(fn($record) => $record->other_phone . ' (' . $record->other_phone_adverb . ')'),
                            ]),
                        
                        Section::make('Masa Pengabdian')
                            ->icon('heroicon-m-clock')
                            ->schema([
                                TextEntry::make('entry_date')
                                    ->label('TMT')
                                    ->inlineLabel()
                                    ->alignEnd()
                                    ->date(),
                                TextEntry::make('work_period')
                                    ->label('Masa Kerja')
                                    ->inlineLabel()
                                    ->alignEnd()
                                    ->state(fn ($record) => $record->entry_date 
                                        ? number_format(Carbon::parse($record->entry_date)->diffInYears(Carbon::now()), 1) . ' Tahun' 
                                        : '-')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('retirement_date')
                                    ->label('Pensiun')
                                    ->inlineLabel()
                                    ->alignEnd()
                                    ->date()
                                    ->color('danger'),
                            ]),

                        Section::make('Data Kepegawaian')
                            ->icon('heroicon-m-briefcase')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextEntry::make('leaves_taken')
                                        ->label('Cuti Terpakai')
                                        ->state(function ($record) {
                                            $staff = Auth::user()->staff;
                                            $isPermanent = $staff->staffStatus->name == 'Tetap';

                                            $isContract = $staff->staffStatus->name == 'Kontrak' 
                                                && $staff->entry_date 
                                                && Carbon::parse($staff->entry_date)->diffInMonths(now()) >= 12;

                                            if (!$isPermanent && !$isContract) {
                                                return 'N/A';
                                            }

                                            return $record->leave()
                                                ->where('staff_id', $record->id)
                                                ->where('type', 'Cuti')
                                                ->whereIn('subtype', ['Tahunan', 'Darurat'])
                                                ->whereYear('start_date', now()->year)
                                                ->where(function ($query) {
                                                    $query->where('status', '!=', 'Ditolak')
                                                        ->orWhere('is_verified', '!=', 0);
                                                })
                                                ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1')) . ' Hari';
                                        })
                                        ->extraAttributes([
                                            'class' => '-mt-2'
                                        ])
                                        ->color('danger'),

                                    TextEntry::make('leaves_remaining')
                                        ->label('Sisa Cuti')
                                        ->state(function ($record) {
                                            $staff = Auth::user()->staff;
                                            $isPermanent = $staff->staffStatus->name == 'Tetap';

                                            $isContract = $staff->staffStatus->name == 'Kontrak' 
                                                && $staff->entry_date 
                                                && Carbon::parse($staff->entry_date)->diffInMonths(now()) >= 12;

                                            if (!$isPermanent && !$isContract) {
                                                return 'N/A';
                                            }

                                            $quota = setting('max_leave_days'); 

                                            if (Carbon::parse($record->entry_date)->year == now()->year + 1) {
                                                $monthJoin = Carbon::parse($staff->entry_date)->month;
                                                $quota -= $monthJoin;
                                            }
                                            
                                            $taken = $record->leave()
                                                ->where('staff_id', $record->id)
                                                ->where('type', 'Cuti')
                                                ->whereIn('subtype', ['Tahunan', 'Darurat'])
                                                ->whereYear('start_date', now()->year)
                                                ->where(function ($query) {
                                                        $query->where('status', '!=', 'Ditolak')
                                                            ->orWhere('is_verified', '!=', 0);
                                                    })
                                                ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1'));
                                                
                                            return ($quota - $taken) . ' Hari';
                                        })
                                        ->extraAttributes([
                                            'class' => '-mt-2'
                                        ])
                                        ->weight(FontWeight::Bold)
                                        ->color('success'),
                                    TextEntry::make('permission_taken')
                                        ->label('Izin Terpakai')
                                        ->state(function ($record) {
                                            return $record->leave()
                                                ->where('staff_id', $record->id)
                                                ->where('type', 'Izin')
                                                ->where('subtype', 'Non-Sakit')
                                                ->whereYear('start_date', now()->year)
                                                ->where(function ($query) {
                                                        $query->where('status', '!=', 'Ditolak')
                                                            ->orWhere('is_verified', '!=', 0);
                                                    })
                                                ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1')) . ' Hari';
                                        })
                                        ->extraAttributes([
                                            'class' => '-mt-2'
                                        ])
                                        ->color('danger'),

                                    TextEntry::make('permission_remaining')
                                        ->label('Sisa Izin')
                                        ->state(function ($record) {
                                            $quota = setting('max_permission_days'); 
                                            
                                            $taken = $record->leave()
                                                ->where('staff_id', $record->id)
                                                ->where('type', 'Izin')
                                                ->where('subtype', 'Non-Sakit')
                                                ->whereYear('start_date', now()->year)
                                                ->where(function ($query) {
                                                        $query->where('status', '!=', 'Ditolak')
                                                            ->orWhere('is_verified', '!=', 0);
                                                    })
                                                ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1'));
                                                
                                            return ($quota - $taken) . ' Hari';
                                        })
                                        ->extraAttributes([
                                            'class' => '-mt-2'
                                        ])
                                        ->weight(FontWeight::Bold)
                                        ->color('success'),
                                ]),

                                TextEntry::make('completeness')
                                    ->label('Administrasi')
                                    ->inlineLabel()
                                    ->alignEnd()
                                    ->state(function ($record) {
                                        $admin = $record->administration;

                                        if (! $admin) {
                                            return '0%';
                                        }

                                        $fields = ['mcu', 'utw'];
                                        if ($record->group_id < 9 && $record->group_id != 1){
                                            array_push($fields, 'sip', 'str', 'spk', 'rkk');
                                        }

                                        $totalFields = count($fields);
                                        $filledFields = 0;

                                        foreach ($fields as $field) {
                                            if (!empty($admin->$field)) {
                                                $filledFields++;
                                            }
                                        }

                                        $percentage = ($filledFields / $totalFields) * 100;
                                        
                                        return number_format($percentage, 0) . '%';
                                    })
                                    ->badge()
                                    ->icon(function ($record) {
                                        if ($record->administration?->is_verified) {
                                            return 'heroicon-m-check-badge';
                                        }
                                        return 'heroicon-m-clipboard-document-list';
                                    })
                                    ->color(function ($record) {
                                        $admin = $record->administration;

                                        if (! $admin) return 'danger';

                                        if ($admin->is_verified) {
                                            return 'success';
                                        }

                                        return 'warning'; 
                                    })
                                    ->tooltip(function ($record) {
                                        $admin = $record->administration;
                                        if ($admin?->is_verified) return 'Data Terverifikasi Valid';
                                        return 'Menunggu Verifikasi / Data Belum Lengkap';
                                    }),

                                TextEntry::make('performance')
                                    ->label('Kinerja')
                                    ->inlineLabel()
                                    ->alignEnd()
                                    ->state(function ($record) {
                                        $lastScore = $record->performance()
                                            ->latest('period_id')
                                            ->first();
                                            
                                        return $lastScore ? $lastScore->appraisal?->score : '-';
                                    })
                                    ->badge()
                                    ->color(function ($record) {
                                        $lastScore = $record->performance()
                                            ->latest('period_id')
                                            ->first();

                                        if (!$lastScore) {
                                            return 'gray';
                                        }

                                        if ($lastScore->appraisal === null) {
                                            return 'gray';
                                        }
                                            
                                        return match (true) {
                                            $lastScore->appraisal?->score >= 85 => 'info', 
                                            $lastScore->appraisal?->score >= 70 => 'success', 
                                            $lastScore->appraisal?->score >= 50 => 'warning', 
                                            $lastScore->appraisal?->score > 0   => 'danger',  
                                            default      => 'gray'
                                        };
                                    }),

                                TextEntry::make('overtime')
                                    ->label('Lembur')
                                    ->inlineLabel()
                                    ->alignEnd()
                                    ->state(function ($record) {
                                        $hours = $record->overtime()
                                            ->whereMonth('overtime_date', now()->month)
                                            ->whereYear('overtime_date', now()->year)
                                            ->sum('hours');
                                            
                                        return $hours ? $hours . ' Jam' : '0 Jam';
                                    })
                                    ->icon('heroicon-m-clock'),
                                TextEntry::make('trainings')
                                    ->label('Pelatihan')
                                    ->inlineLabel()
                                    ->alignEnd()
                                    ->state(function ($record) {
                                        $hours = $record->training()
                                            ->whereYear('training_date', now()->year)
                                            ->sum('duration');

                                        return $hours ? $hours . '/20 Jam' : '0 Jam';
                                    })
                                    ->badge()
                                    ->color(function ($record) {
                                        $hours = $record->training()
                                            ->whereYear('training_date', now()->year)
                                            ->sum('duration');

                                        if (!$hours) {
                                            return 'gray';
                                        }

                                        return match (true) {
                                            $hours >= 20 => 'success', 
                                            $hours < 20 => 'warning', 
                                            default => 'gray'
                                        };
                                    }),
                            ]),
                    ])->columnSpan(['md' => 1]),
                ]),

        ])
        ->columns(1);
    }

    protected static function getPdfEntry(string $field, string $label, string $modelName): TextEntry
    {
        return TextEntry::make($field)
            ->label($label)
            ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
            ->suffixAction(
                Action::make('show_pdf_' . str_replace('.', '_', $field))
                    ->icon('heroicon-o-eye')
                    ->label('Lihat')
                    ->button()
                    ->color('gray')
                    ->size('xs')
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalContent(function ($record) use ($modelName, $field) {
                        $targetField = $field;
                        
                        if (str_contains($field, '.')) {
                            // Ambil bagian setelah titik. Contoh: 'contract.decree' -> ambil 'decree'
                            $parts = explode('.', $field);
                            $targetField = end($parts);
                        }

                        return view('filament.components.preview-pdf-2', [
                            'url' => route('preview.administration', [
                                'model' => $modelName, // 'contract'
                                'id' => $record->id,   // KEMBALI GUNAKAN ID STAFF (Parent)
                                'field' => $targetField // 'decree'
                            ])
                        ]);
                    })
            );
    }
}
