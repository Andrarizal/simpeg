<?php

namespace App\Filament\Resources\Leaves\Schemas;

use App\Models\Leave;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class LeaveForm
{
    use WithFileUploads;

    public static function configure(Schema $schema): Schema
    {
        // Cek jabatan user
        $staff = Auth::user()->staff;
        // Cek jika superadmin
        $chair = !$staff ? 1 : $staff->chair_id;
        
        return $schema
            ->components([
                ToggleButtons::make('type')
                    ->label('Jenis')
                    ->options(['Cuti' => 'Cuti', 'Izin' => 'Izin'])
                    ->inline()
                    ->default(fn() => 'Cuti')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, callable $get) use ($staff) {
                        // Cek value dari type dan staff
                        $type = $get('type');
                        if (!$staff || !$type) {
                            $set('remaining', null);
                            return;
                        }

                        // Set nilai dari sisa cuti/izin menggunakan helper
                        $set('remaining', static::calculateRemaining($type, $staff));
                    }),
                Select::make('subtype')
                    ->label(fn (callable $get) => 'Jenis ' . $get('type'))
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
                    ->dehydrated(true)
                    ->reactive()
                    ->native(false),
                Select::make('staff_id')
                    ->label('Nama Pegawai')
                    ->relationship('staff', 'name')
                    ->required()
                    ->default(fn() => $chair > 1 ? $staff->id : null)
                    ->disabled(fn() => $chair > 1 ? true : false)
                    ->dehydrated(true),
                Textarea::make('reason')
                    ->label('Keperluan')
                    ->required(),
                DatePicker::make('start_date')
                    ->label('Dari Tanggal')
                    ->minDate(function (callable $get) {
                        $type = $get('subtype'); 

                        if (in_array($type, ['Tahunan', 'Melahirkan'])) {
                            return Carbon::now()->addMonth(); 
                        }
                        return Carbon::tomorrow(); 
                    })
                    ->maxDate(date('Y-12-31'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        // reset end_date ketika start_date berubah
                        $set('end_date', null);
                    })
                    ->native(false),
                DatePicker::make('end_date')
                    ->label('Sampai Tanggal')
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
                        return $start ? Carbon::parse($start)->addDays($limit) : null; // misalnya maksimal 14 hari
                    })
                    ->reactive()
                    ->disabled(fn (callable $get) => blank($get('start_date')))
                    ->required()
                    ->native(false),
                TextInput::make('remaining')
                    ->label(fn (callable $get) => 'Sisa ' . $get('type'))
                    ->numeric()
                    ->disabled()
                    ->visible(fn(callable $get) => $get('subtype') == 'Tahunan' || $get('subtype') == 'Non-Sakit' ? true : false)
                    ->default(fn() => static::calculateRemaining('Cuti', $staff))
                    ->dehydrated(true),
                Select::make('replacement_id')
                    ->label('Nama Pengganti')
                    ->relationship('replacement', 'name', modifyQueryUsing: function ($query) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;

                        if ($user && $user->staff_id) {
                            // ambil staff yang satu level jabatan atau lebih rendah
                            $query->where('id', '!=', $user->staff_id)
                                ->whereHas('chair', function ($q) use ($user) {
                                    if ($user->staff->chair->level == 4){
                                        $q->where('head_id', $user->staff->chair->head_id);
                                    } else {
                                        $q->where('head_id', $user->staff->chair_id);
                                    }
                                    $q->where('level', '>=', $user->staff->chair->level);
                                });
                        }
                    })
                    ->required()
                    ->native(false),
                FileUpload::make('evidence')
                    ->label(fn (callable $get) => 'Surat ' . $get('type'))
                    ->disk('public')
                    ->visibility('public')
                    ->directory('surat-cuti') // folder penyimpanan di storage/app/public/surat-cuti
                    ->visible(fn (callable $get) => in_array($get('subtype'), ['Melahirkan', 'Duka', 'Sakit']))
                    ->required(fn (callable $get) => in_array($get('subtype'), ['Melahirkan', 'Duka', 'Sakit']))
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->maxSize(2048) // maksimal 2MB
                    ->helperText('Unggah surat cuti/izin dalam format PDF atau gambar'),
                Select::make('status')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Disetujui Koordinator' => 'Disetujui Koordinator',
                        'Disetujui Kasi' => 'Disetujui Kasi',
                        'Disetujui Direktur' => 'Disetujui direktur',
                        'Ditolak' => 'Ditolak',
                    ])
                    ->required()
                    ->default(fn() => $chair == 1 ? 'Disetujui Direktur' : 'Menunggu')
                    ->visible(fn() => $chair > 1 ? false : true)
                    ->disabled(fn() => $chair > 1 ?  : false)
                    ->dehydrated(true),
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
