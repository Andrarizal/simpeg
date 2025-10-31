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
use Filament\Forms\Components\Select;
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
            ->icon('heroicon-o-arrow-up-tray')
            ->modalHeading('Impor Data Pegawai')
            ->modalDescription('Unggah file Excel atau CSV berisi data pegawai. Sistem akan memperbarui data yang sudah ada (berdasarkan NIK).')
            ->form([
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

                            if ($isExist && $mode === 'skip') continue;

                            $staff = [
                                'nip' => $row[2],
                                'name' => $row[0],
                                'birth_place' => $row[3] ?? null,
                                'birth_date' => isset($row[4]) ? Carbon::createFromFormat('d/m/Y', trim($row[4])) : null,
                                'sex' => $row[5] ?? 'L',
                                'marital' => $row[6] ?? 'Lajang',
                                'phone' => $row[7] ?? null,
                                'address' => $row[8] ?? null,
                                'email' => $row[9] ?? null,
                                'other_phone' => $row[10] ?? null,
                                'other_phone_adverb' => $row[11] ?? 'Lainnya',
                                'entry_date' => isset($row[12]) ? Carbon::createFromFormat('d/m/Y', trim($row[12])) : null,
                                'retirement_date' => isset($row[13]) ? Carbon::createFromFormat('d/m/Y', trim($row[13])) : null,
                                'staff_status_id' => $this->mapStatus($row[14]),
                                'chair_id' => $this->mapChair($row[15]),
                                'group_id' => $this->mapGroup($row[16]),
                                'unit_id' => $this->mapUnit($row[17]),
                            ];

                            $staff_entry_education = [
                                'level' => $row[18],
                                'institution' => $row[19] ?? null,
                                'certificate_number' => $row[20] ?? null,
                                'certificate_date' => Carbon::createFromFormat('d/m/Y', trim($row[21])),
                                'nonformal_education' => $row[22] ?? null,
                                'adverb' => $row[23] ?? null,
                            ];

                            $staff_work_education = [
                                'level' => $row[24],
                                'major' => $row[25] ?? null,
                                'institution' => $row[26] ?? null,
                                'certificate_number' => $row[27] ?? null,
                                'certificate_date' => Carbon::createFromFormat('d/m/Y', trim($row[28])),
                            ];

                            $staff_work_experience = [
                                'institution' => $row[29],
                                'work_length' => $row[30] ?? null,
                                'admission' => $row[31] ?? null,
                            ];

                            $staff_contract = [
                                'contract_number' => $row[32],
                                'start_date' => Carbon::createFromFormat('d/m/Y', trim($row[33])),
                                'end_date' => Carbon::createFromFormat('d/m/Y', trim($row[33])),
                            ];

                            $staff_appointment = [
                                'decree_number' => $row[35],
                                'decree_date' => Carbon::createFromFormat('d/m/Y', trim($row[36])),
                                'class' => $row[37] ?? null,
                            ];

                            $staff_adjustment = [
                                'decree_number' => $row[38],
                                'decree_date' => Carbon::createFromFormat('d/m/Y', trim($row[39])),
                                'class' => $row[40] ?? null,
                            ];
                            
                            $newRow = Staff::updateOrCreate(
                            ['nik' => $row[1]],
                                $staff
                            );

                            if (!empty($row[18])) {
                                StaffEntryEducation::updateOrCreate(['staff_id' => $newRow['id']],
                                    $staff_entry_education
                                );
                            }

                            if (!empty($row[24])) {
                                StaffWorkEducation::updateOrCreate(['staff_id' => $newRow['id']],
                                    $staff_work_education
                                );
                            }

                            if (!empty($row[29])) {
                                StaffWorkExperience::updateOrCreate(['staff_id' => $newRow['id']],
                                    $staff_work_experience
                                );
                            }

                            if (!empty($row[32])) {
                                StaffContract::updateOrCreate(['staff_id' => $newRow['id']],
                                    $staff_contract
                                );
                            }

                            if (!empty($row[35])) {
                                StaffAppointment::updateOrCreate(['staff_id' => $newRow['id']],
                                    $staff_appointment
                                );
                            }

                            if (!empty($row[38])) {
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
