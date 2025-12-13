<?php

namespace App\Filament\Resources\Staff\Pages;

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
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class ListStaff extends ListRecords
{
    use WithFileUploads;
    
    protected static string $resource = StaffResource::class;

    protected static ?string $title = 'Daftar Pegawai';

    public $file;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Impor Pegawai')
                ->color('warning')
                ->icon('heroicon-o-arrow-up-tray')
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
                    try {
                        $fullPath = storage_path('app/public/' . $data['file']);
                        $rows = Excel::toCollection(null, $fullPath)->first();

                        $mode = $data['mode'];
                        
                        DB::transaction(function () use ($rows, $mode) {
                            $headersCheck = 0;

                            foreach ($rows as $row) {
                                // dump($row);
                                if ($headersCheck++ <= 1 || empty($row[0]) || empty($row[1])) continue;

                                $isExist = Staff::where('nik', $row[1])->first();

                                if ($isExist && $mode == 'skip') continue;

                                $staff = [
                                    'nip' => $row[2],
                                    'name' => $row[0],
                                    'birth_place' => $row[3] ?? null,
                                    'birth_date' => isset($row[4]) ? Carbon::createFromFormat('d/m/Y', trim($row[4])) : null,
                                    'sex' => $row[5] ?? 'L',
                                    'marital' => $row[6] ?? 'Lajang',
                                    'phone' => $row[7] ?? null,
                                    'origin' => $row[8] ?? null,
                                    'domicile' => $row[9] ?? null,
                                    'email' => $row[10] ?? null,
                                    'other_phone' => $row[11] ?? null,
                                    'other_phone_adverb' => $row[12] ?? 'Lainnya',
                                    'entry_date' => isset($row[13]) ? Carbon::createFromFormat('d/m/Y', trim($row[13])) : null,
                                    'retirement_date' => isset($row[14]) ? Carbon::createFromFormat('d/m/Y', trim($row[4]))->addYear(56)->format('Y-m-d') : null,
                                    'staff_status_id' => $this->mapStatus($row[15]),
                                    'chair_id' => $this->mapChair($row[16]),
                                    'group_id' => $this->mapGroup($row[17]),
                                    'unit_id' => $this->mapUnit($row[18]),
                                ];

                                $staff_entry_education = [
                                    'level' => $row[19],
                                    'institution' => $row[20] ?? null,
                                    'certificate_number' => $row[21] ?? null,
                                    'certificate_date' => Carbon::createFromFormat('d/m/Y', trim($row[22])),
                                    'nonformal_education' => $row[23] ?? null,
                                    'adverb' => $row[24] ?? null,
                                ];

                                $staff_work_education = [
                                    'level' => $row[25],
                                    'major' => $row[26] ?? null,
                                    'institution' => $row[27] ?? null,
                                    'certificate_number' => $row[28] ?? null,
                                    'certificate_date' => Carbon::createFromFormat('d/m/Y', trim($row[29])),
                                ];

                                $staff_work_experience = [
                                    'institution' => $row[30],
                                    'work_length' => $row[31] ?? null,
                                    'admission' => $row[32] ?? null,
                                ];

                                $staff_contract = [
                                    'contract_number' => $row[33],
                                    'start_date' => Carbon::createFromFormat('d/m/Y', trim($row[34])),
                                    'end_date' => Carbon::createFromFormat('d/m/Y', trim($row[35])),
                                ];

                                $staff_appointment = [
                                    'decree_number' => $row[36],
                                    'decree_date' => Carbon::createFromFormat('d/m/Y', trim($row[37])),
                                    'class' => $row[38] ?? null,
                                ];

                                $staff_adjustment = [
                                    'decree_number' => $row[39],
                                    'decree_date' => Carbon::createFromFormat('d/m/Y', trim($row[40])),
                                    'class' => $row[41] ?? null,
                                ];
                                
                                $newRow = Staff::updateOrCreate(
                                ['nik' => $row[1]],
                                    $staff
                                );

                                if (!empty($row[19])) {
                                    StaffEntryEducation::updateOrCreate(['staff_id' => $newRow['id']],
                                        $staff_entry_education
                                    );
                                }

                                if (!empty($row[25])) {
                                    StaffWorkEducation::updateOrCreate(['staff_id' => $newRow['id']],
                                        $staff_work_education
                                    );
                                }

                                if (!empty($row[30])) {
                                    StaffWorkExperience::updateOrCreate(['staff_id' => $newRow['id']],
                                        $staff_work_experience
                                    );
                                }

                                if (!empty($row[33])) {
                                    StaffContract::updateOrCreate(['staff_id' => $newRow['id']],
                                        $staff_contract
                                    );
                                }

                                if (!empty($row[36])) {
                                    StaffAppointment::updateOrCreate(['staff_id' => $newRow['id']],
                                        $staff_appointment
                                    );
                                }

                                if (!empty($row[39])) {
                                    StaffAdjustment::updateOrCreate(['staff_id' => $newRow['id']],
                                        $staff_adjustment
                                    );
                                }
                            }
                        });
                    } finally {
                        $relativePath = str_replace(storage_path('app/public/'), '', $fullPath);

                        if (Storage::disk('public')->exists($relativePath)) Storage::disk('public')->delete($relativePath);
                    }
                    
                    Notification::make()
                    ->title('Data pegawai berhasil diimpor!')
                    ->success()
                    ->send();
                }),
            CreateAction::make(),
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
}
