<?php

namespace App\Filament\Resources\Staff\Pages;

use App\Exports\StaffExport;
use App\Filament\Resources\Staff\StaffResource;
use App\Models\Chair;
use App\Models\Group;
use App\Models\Staff;
use App\Models\StaffAdjustment;
use App\Models\StaffAppointment;
use App\Models\StaffContract;
use App\Models\StaffEntryEducation;
use App\Models\StaffStatus;
use App\Models\StaffWorkEducation;
use App\Models\StaffWorkExperience;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ListStaff extends ListRecords
{
    use WithFileUploads;
    
    protected static string $resource = StaffResource::class;

    protected static ?string $title = 'Daftar Pegawai';

    public $file;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Expor Pegawai')
                ->icon('heroicon-o-arrow-up-on-square')
                ->color('info')
                ->action(function () {
                    $namaFile = 'Data Kepegawaian_' . date('Y-m-d') . '.xlsx';
                    return Excel::download(new StaffExport(), $namaFile);
                }),
            Action::make('import')
                ->label('Impor Pegawai')
                ->color('warning')
                ->icon('heroicon-o-arrow-down-on-square')
                ->modalHeading('Impor Data Pegawai')
                ->modalDescription('Unggah file Excel atau CSV berisi data pegawai. Sistem akan memperbarui data yang sudah ada (berdasarkan NIK).')
                ->schema([
                    Grid::make([
                        'default' => 1, // default 1 kolom di layar kecil
                        'sm' => 2,      // jadi 2 kolom di layar lebar
                    ])
                    ->schema([
                        FileUpload::make('file')
                            ->label('File Excel / CSV')
                            ->required()
                            ->acceptedFileTypes([
                                'text/csv',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->disk('public') 
                            ->directory('imports'),
                        Radio::make('mode')
                            ->label('Tindakan jika data dengan NIK yang sama sudah ada')
                            ->options([
                                'overwrite' => 'Timpa data lama (Overwrite)',
                                'skip' => 'Lewati data yang sudah ada (Skip)',
                            ])
                            ->default('overwrite')
                            ->required(),
                        Action::make('download_template')
                            ->label('Unduh Template')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->url(asset('templates/staff_import_template.xlsx'))
                            ->color('gray'),
                    ])
                ])
                ->action(function (array $data) {
                    $countUsers = 0;
                    set_time_limit(60);
                    try {
                        $fullPath = storage_path('app/public/' . $data['file']);
                        $rows = Excel::toCollection(null, $fullPath)->first();

                        $mode = $data['mode'];
                        
                        DB::transaction(function () use ($rows, $mode, $countUsers) {
                            $headersCheck = 0;

                            $parseDate = function ($value) {
                                if (empty(trim($value))) return null;
                                try {
                                    return is_numeric($value) 
                                        ? Carbon::instance(Date::excelToDateTimeObject($value))
                                        : Carbon::createFromFormat('d/m/Y', trim($value));
                                } catch (\Exception $e) {
                                    return null;
                                }
                            };

                            foreach ($rows as $row) {
                                if ($headersCheck++ <= 1 || empty($row[0]) || empty($row[1])) continue;

                                $isExist = Staff::where('nik', $row[1])->first();

                                if ($isExist && $mode == 'skip') continue;

                                $birthDate = $parseDate($row[4]);

                                $staff = [
                                    'nip' => $row[2],
                                    'name' => $row[0],
                                    'birth_place' => $row[3] ?? null,
                                    'birth_date' => $birthDate,
                                    'sex' => $row[5] ?? 'L',
                                    'marital' => $row[6] ?? 'Lajang',
                                    'phone' => $row[7] ?? null,
                                    'origin' => $row[8] ?? null,
                                    'domicile' => $row[9] ?? null,
                                    'email' => $row[10] ?? null,
                                    'other_phone' => $row[11] ?? null,
                                    'other_phone_adverb' => $row[12] ?? 'Lainnya',
                                    'entry_date' => $parseDate($row[13]),
                                    'retirement_date' => $birthDate ? $birthDate->copy()->addYears(56)->format('Y-m-d') : null, 
                                    'staff_status_id' => $this->mapStatus($row[15]),
                                    'chair_id' => $this->mapChair($row[16]),
                                    'group_id' => $this->mapGroup($row[17]),
                                    'unit_id' => $this->mapUnit($row[18]),
                                ];
                                
                                $newRow = Staff::updateOrCreate(
                                    ['nik' => $row[1]],
                                    $staff
                                );

                                if (!empty($row[19])) {
                                    StaffEntryEducation::updateOrCreate(
                                        ['staff_id' => $newRow->id],
                                        [
                                            'level' => $row[19],
                                            'institution' => $row[20] ?? null,
                                            'certificate_number' => $row[21] ?? null,
                                            'certificate_date' => $parseDate($row[22]),
                                            'nonformal_education' => $row[23] ?? null,
                                            'adverb' => $row[24] ?? null,
                                        ]
                                    );
                                }

                                if (!empty($row[25])) {
                                    StaffWorkEducation::updateOrCreate(
                                        ['staff_id' => $newRow->id],
                                        [
                                            'level' => $row[25],
                                            'major' => $row[26] ?? null,
                                            'institution' => $row[27] ?? null,
                                            'certificate_number' => $row[28] ?? null,
                                            'certificate_date' => $parseDate($row[29]),
                                        ]
                                    );
                                }

                                if (!empty($row[30])) {
                                    StaffWorkExperience::updateOrCreate(
                                        ['staff_id' => $newRow->id],
                                        [
                                            'institution' => $row[30],
                                            'work_length' => $row[31] ?? null,
                                            'admission' => $row[32] ?? null,
                                        ]
                                    );
                                }

                                if (!empty($row[33])) {
                                    StaffContract::updateOrCreate(
                                        ['staff_id' => $newRow->id],
                                        [
                                            'contract_number' => $row[33],
                                            'start_date' => $parseDate($row[34]),
                                            'end_date' => $parseDate($row[35]),
                                        ]
                                    );
                                }

                                if (!empty($row[36])) {
                                    StaffAppointment::updateOrCreate(
                                        ['staff_id' => $newRow->id],
                                        [
                                            'decree_number' => $row[36],
                                            'decree_date' => $parseDate($row[37]),
                                            'class' => $row[38] ?? null,
                                        ]
                                    );
                                }

                                if (!empty($row[39])) {
                                    StaffAdjustment::updateOrCreate(
                                        ['staff_id' => $newRow->id],
                                        [
                                            'decree_number' => $row[39],
                                            'decree_date' => $parseDate($row[40]),
                                            'class' => $row[41] ?? null,
                                        ]
                                    );
                                }

                                if (!empty($row[10])) {
                                    User::updateOrCreate(
                                        ['email' => $row[10]],
                                        [
                                            'name' => $row[0],
                                            'password' => bcrypt($birthDate ? $birthDate->format('dmY') : '123456'),
                                            'role_id' => 2,
                                            'staff_id' => $newRow->id
                                        ]
                                    );
                                }

                                $countUsers++;
                            }
                        });
                    } finally {
                        $relativePath = str_replace(storage_path('app/public/'), '', $fullPath);

                        if (Storage::disk('public')->exists($relativePath)) {
                            Storage::disk('public')->delete($relativePath);
                        }
                    }
                    
                    Notification::make()
                        ->title($countUsers . ' Data pegawai berhasil diimpor!')
                        ->success()
                        ->send();
                }),
            CreateAction::make()
                ->label('Daftarkan Pegawai'),
        ];
    }

    protected function getBreadcrumbTitle(): string
    {
        return 'Daftar Pegawai';
    }

    private function mapStatus($name)
    {
        return StaffStatus::firstOrCreate(['name' => trim($name ?? 'Tidak Diketahui')])->id;
    }

    private function mapUnit($name)
    {
        return Unit::firstOrCreate(['name' => trim($name ?? 'Umum')])->id;
    }

    private function mapGroup($name)
    {
        return Group::firstOrCreate(['name' => trim($name ?? 'Non-Kelompok')])->id;
    }

    private function mapChair($name)
    {
        return Chair::firstOrCreate(['name' => trim($name ?? 'Tidak Ada')])->id;
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();
        if (!$user || !$user->staff || !$user->staff->chair) {
            return false; 
        }

        return $user->role_id == 1;
    }
}
