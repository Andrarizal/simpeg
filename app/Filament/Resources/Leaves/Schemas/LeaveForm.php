<?php

namespace App\Filament\Resources\Leaves\Schemas;

use App\Models\Leave;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class LeaveForm
{
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
                Select::make('staff_id')
                    ->label('Nama Pegawai')
                    ->relationship('staff', 'name')
                    ->required()
                    ->default(fn() => $chair > 1 ? $staff->id : null)
                    ->disabled(fn() => $chair > 1 ? true : false)
                    ->dehydrated(true),
                DatePicker::make('start_date')
                    ->label('Dari Tanggal')
                    ->minDate(date("Y-m-d",strtotime("tomorrow")))
                    ->maxDate(date('Y-12-31'))
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Sampai Tanggal')
                    ->minDate(date("Y-m-d",strtotime("tomorrow")))
                    ->maxDate(date('Y-12-31'))
                    ->required(),
                Textarea::make('reason')
                    ->label('Keperluan')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('remaining')
                    ->label('Sisa Cuti')
                    ->numeric()
                    ->disabled()
                    ->visible()
                    ->dehydrated(true),
                Select::make('replacement_id')
                    ->label('Nama Pengganti')
                    ->relationship('replacement', 'name', modifyQueryUsing: function ($query) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;

                        if ($user && $user->staff_id) {
                            // ambil staff yang satu level jabatan atau lebih rendah
                            $query->where('id', '!=', $user->staff_id)
                                ->where('unit_id', $user->staff->unit_id) // Masih Error untuk superadmin
                                ->whereHas('chair', function ($q) use ($user) {
                                    $q->where('level', '>=', $user->staff->chair->level);
                                });
                        }
                    })
                    ->required(),
                Select::make('status')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Disetujui Koordinator' => 'Disetujui Koordinator',
                        'Disetujui Kasi' => 'Disetujui Kasi',
                        'Disetujui Direktur' => 'Disetujui direktur',
                        'Ditolak' => 'Ditolak',
                    ])
                    ->required()
                    ->default(fn() => $chair === 1 ? 'Disetujui Direktur' : 'Menunggu')
                    ->visible(fn() => $chair > 1 ? false : true)
                    ->disabled(fn() => $chair > 1 ?  : false)
                    ->dehydrated(true),
                Textarea::make('Catatan'),
            ]);
    }

    protected static function calculateRemaining(?string $type, $staff): ?int
    {
        if (!$staff) return null;

        if ($type === 'Cuti') {
            // ambil max cuti dari table master dengan helper setting
            $maxLeave = setting('max_leave_days');

            // cocokkan tahun masuk dengan tahun sekarang
            if (date('Y', strtotime($staff->entry_date)) === strval(now()->year)) {
                // kurangi sisa cuti dengan bulan yang sudah lewat
                $maxLeave -= date('m', strtotime($staff->entry_date));
            }

            // cek jumlah cuti yang pernah diambil dalam setahun
            $usedLeave = Leave::where('type', 'Cuti')
                ->where('staff_id', $staff->id)
                ->where('status', '!=', 'Ditolak')
                ->whereYear('start_date', now()->year)
                ->count();

            // kurangi jumlah cuti dengan yang cuti sudah diambil
            return max($maxLeave - $usedLeave, 0);
        }

        if ($type === 'Izin') {
            // ambil max izin dari table master dengan helper setting
            $maxLeave = setting('max_permission_days');

            // ambil izin yang pernah disetujui
            $usedLeave = Leave::where('type', 'Izin')
            ->where('staff_id', $staff->id)
            ->where('status', '!=', 'Ditolak')
            ->whereMonth('start_date', now()->month)
            ->count();
            
            // kurangi dengan izin yang pernah diambil
            return max($maxLeave - $usedLeave, 0);
        }

        return null;
    }

}
