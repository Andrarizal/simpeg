<?php

namespace App\Filament\Resources\Leaves\Schemas;

use App\Models\Leave;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class LeaveForm
{
    use WithFileUploads;

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
                            Section::make('Rincian Pengajuan')
                                ->icon('heroicon-m-clipboard-document-list')
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
                                    ToggleButtons::make('type')
                                        ->label('Jenis Pengajuan')
                                        ->options([
                                            'Cuti' => 'Cuti', 
                                            'Izin' => 'Izin'
                                        ])
                                        ->icons([
                                            'Cuti' => 'heroicon-o-briefcase', 
                                            'Izin' => 'heroicon-o-arrow-right-start-on-rectangle'
                                        ])
                                        ->colors([
                                            'Cuti' => 'info', 
                                            'Izin' => 'warning'
                                        ])
                                        ->inline()
                                        ->inlineLabel()
                                        ->default('Cuti')
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function (callable $set, callable $get) use ($staff) {
                                            $set('subtype', null);
                                            $set('start_date', null);
                                            $set('end_date', null);
                                            $type = $get('type');
                                            if (!$staff || !$type) {
                                                $set('remaining', null);
                                                return;
                                            }
                                            $set('remaining', static::calculateRemaining($type, $staff));
                                        })
                                        ->columnSpan(1),

                                    TextInput::make('remaining')
                                        ->label(fn (callable $get) => 'Sisa Jatah ' . ($get('type') ?? 'Cuti'))
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(true)
                                        ->inlineLabel()
                                        ->prefixIcon('heroicon-m-chart-pie')
                                        ->visible(fn(callable $get) => in_array($get('subtype'), ['Tahunan', 'Non-Sakit']))
                                        ->default(fn() => static::calculateRemaining('Cuti', $staff))
                                        ->extraInputAttributes(['class' => 'font-bold text-primary-600'])
                                        ->columnSpan(1),

                                    Select::make('subtype')
                                        ->label('Kategori')
                                        ->options(function (callable $get) {
                                            if ($get('type') == 'Cuti'){
                                                return [
                                                    'Tahunan' => 'Tahunan',
                                                    'Melahirkan' => 'Melahirkan',
                                                    'Duka' => 'Duka',
                                                    'Menikah' => 'Menikah',
                                                    'Ibadah Haji' => 'Ibadah Haji',
                                                    'Khitan Anak' => 'Khitan Anak',
                                                    'Baptis Anak' => 'Baptis Anak'
                                                ];
                                            }
                                            return [
                                                'Sakit' => 'Sakit',
                                                'Non-Sakit' => 'Non-Sakit'
                                            ];
                                        })
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->reactive()
                                        ->afterStateUpdated(function (callable $set) {
                                            $set('start_date', null);
                                            $set('end_date', null);
                                        })
                                        ->inlineLabel()
                                        ->native(false),
                                    
                                    Textarea::make('reason')
                                        ->label('Keperluan / Alasan')
                                        ->placeholder('Jelaskan detail keperluan cuti/izin Anda...')
                                        ->rows(2)
                                        ->required()
                                        ->inlineLabel()
                                        ->columnSpanFull(),
                                ]),

                            Hidden::make('staff_id')
                                ->visible(function () {
                                    $user = Auth::user();
                                    return optional($user->staff->unit)->work_system != 'Shift';
                                })
                                ->default(fn() => $chair > 1 ? $staff->id : null)
                                ->required(),
                                
                            Section::make('Personil')
                                ->icon('heroicon-m-user-group')
                                ->visible(function () {
                                    $user = Auth::user();
                                    return optional($user->staff->unit)->work_system != 'Tetap';
                                })
                                ->schema([
                                    Grid::make(2)->schema([
                                        Select::make('staff_id')
                                            ->label('Nama Pegawai')
                                            ->relationship('staff', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(fn() => $chair > 1 ? $staff->id : null)
                                            ->disabled(fn() => $chair > 1 ? true : false)
                                            ->dehydrated(true)
                                            ->required(),

                                        Select::make('replacement_id')
                                            ->label('Pegawai Pengganti')
                                            ->relationship('replacement', 'name', modifyQueryUsing: function ($query) {
                                                $user = Auth::user();
                                                $userStaffId = $user->staff_id ?? 1;

                                                if ($user && $user->staff_id) {
                                                    $query->where('id', '!=', $userStaffId)
                                                        ->whereHas('chair', function ($q) use ($user) {
                                                            if (optional($user->staff->chair)->level == 4){
                                                                $q->where('head_id', $user->staff->chair->head_id);
                                                            } else {
                                                                $q->where('head_id', $user->staff->chair_id);
                                                            }
                                                            $q->where('level', '>=', optional($user->staff->chair)->level);
                                                        });
                                                }
                                            })
                                            ->visible(function () {
                                                $user = Auth::user();
                                                return optional($user->staff->unit)->work_system != 'Tetap';
                                            })
                                            ->required(function () {
                                                $user = Auth::user();
                                                return optional($user->staff->unit)->work_system != 'Tetap';
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->native(false),
                                    ]),
                                ]),
                        ])->columnSpan(['lg' => 2]),


                    Group::make()
                        ->schema([
                            Section::make('Waktu Pelaksanaan')
                                ->icon('heroicon-m-calendar-days')
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
                                    DatePicker::make('start_date')
                                        ->label('Mulai Tanggal')
                                        ->prefixIcon('heroicon-m-calendar')
                                        ->minDate(function (callable $get) {
                                            $type = $get('subtype'); 
                                            if (in_array($type, ['Tahunan', 'Melahirkan'])) {
                                                return Carbon::now()->addMonth()->addDay(); 
                                            }
                                            return Carbon::tomorrow(); 
                                        })
                                        ->maxDate(date('Y-12-31'))
                                        ->disabled(fn (callable $get) => blank($get('subtype')))
                                        ->required()
                                        ->reactive()
                                        ->helperText(fn (callable $get) => $get('subtype') ? 'Tanggal mulai bersifat relatif.' : 'Pilih kategori dahulu.')
                                        ->afterStateUpdated(fn (callable $set) => $set('end_date', null))
                                        ->native(false),
    
                                    DatePicker::make('end_date')
                                        ->label('Sampai Tanggal')
                                        ->prefixIcon('heroicon-m-calendar')
                                        ->minDate(fn (callable $get) => $get('start_date'))
                                        ->maxDate(function (callable $get) {
                                            $start = $get('start_date');
                                            $subtype = $get('subtype');
                                            $limit = match ($subtype) {
                                                'Tahunan' => 6,
                                                'Melahirkan' => 90,
                                                'Duka' => 2,
                                                'Menikah' => 3,
                                                'Ibadah Haji' => 40,
                                                'Khitan Anak' => 1,
                                                'Baptis Anak' => 1,
                                                'Non-Sakit' => 1,
                                                'Sakit' => 30,
                                                default => 30
                                            };
                                            return $start ? Carbon::parse($start)->addDays($limit) : null;
                                        })
                                        ->reactive()
                                        ->disabled(fn (callable $get) => blank($get('start_date')))
                                        ->required()
                                        ->native(false)
                                        ->helperText(fn (callable $get) => $get('start_date') ? 'Maksimal durasi bersifat relatif.' : 'Pilih tanggal mulai dahulu.'),
                                ]),

                            Section::make('Lampiran Pendukung')
                                ->icon('heroicon-m-paper-clip')
                                ->hidden(fn (callable $get) => !in_array($get('subtype'), ['Melahirkan', 'Duka', 'Sakit']))
                                ->schema([
                                    FileUpload::make('evidence')
                                        ->hiddenLabel()
                                        ->disk('public')
                                        ->directory('surat-cuti')
                                        ->required(fn (callable $get) => !in_array($get('subtype'), ['Melahirkan', 'Duka', 'Sakit']))
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                        ->maxSize(2048)
                                        ->columnSpanFull(),
                                ])
                                ->compact(),

                            Section::make('Status Persetujuan')
                                ->icon('heroicon-m-check-badge')
                                ->visible(fn() => $chair == 1)
                                ->schema([
                                    Select::make('status')
                                        ->hiddenLabel()
                                        ->options([
                                            'Menunggu' => 'Menunggu',
                                            'Disetujui Koordinator' => 'Disetujui Koordinator',
                                            'Disetujui Kasi' => 'Disetujui Kasi',
                                            'Disetujui Direktur' => 'Disetujui Direktur',
                                            'Ditolak' => 'Ditolak',
                                        ])
                                        ->native(false)
                                        ->selectablePlaceholder(false),
                                ])
                                ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-900']),

                        ])->columnSpan(['lg' => 1]),
                ])->columnSpanFull(),
            ]);
    }

    protected static function calculateRemaining(?string $type, $staff): ?int
    {
        if (!$staff) return null;

        if ($type == 'Cuti') {
            // ambil max cuti dari table master dengan helper setting
            $maxLeave = setting('max_leave_days');

            // cocokkan tahun masuk dengan tahun sekarang
            if (date('Y', strtotime($staff->entry_date)) == strval(now()->year)) {
                // kurangi sisa cuti dengan bulan yang sudah lewat
                $maxLeave -= date('m', strtotime($staff->entry_date));
            }

            // cek jumlah cuti yang pernah diambil dalam setahun
            $usedLeave = Leave::where('type', 'Cuti')
                ->where('subtype', 'Tahunan')
                ->where('staff_id', $staff->id)
                ->where('status', '!=', 'Ditolak')
                ->whereYear('start_date', now()->year)
                ->get()
                ->sum(function ($leave) {
                    $start = Carbon::parse($leave->start_date);
                    $end = Carbon::parse($leave->end_date);
                    return $start->diffInDays($end) + 1; // +1 agar termasuk hari pertama
                });

            // kurangi jumlah cuti dengan yang cuti sudah diambil
            return max($maxLeave - $usedLeave, 0);
        }

        if ($type == 'Izin') {
            // ambil max izin dari table master dengan helper setting
            $maxLeave = setting('max_permission_days');

            // cocokkan tahun masuk dengan tahun sekarang
            if (date('Y', strtotime($staff->entry_date)) == strval(now()->year)) {
                // kurangi sisa cuti dengan bulan yang sudah lewat
                $maxLeave -= ceil(date('m', strtotime($staff->entry_date)) / 2);
            }

            // ambil izin yang pernah disetujui
            $usedLeave = Leave::where('type', 'Izin')
            ->where('subtype', 'Non-Sakit')
            ->where('staff_id', $staff->id)
            ->where('status', '!=', 'Ditolak')
            ->whereYear('start_date', now()->year)
            ->get()
            ->sum(function ($leave) {
                $start = Carbon::parse($leave->start_date);
                $end = Carbon::parse($leave->end_date);
                return $start->diffInDays($end); // +1 agar termasuk hari pertama
            });
            
            // kurangi dengan izin yang pernah diambil
            return max($maxLeave - $usedLeave, 0);
        }

        return null;
    }

}
