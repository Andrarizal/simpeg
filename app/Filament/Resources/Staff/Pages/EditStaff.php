<?php

namespace App\Filament\Resources\Staff\Pages;

use App\Filament\Resources\Profiles\ProfileResource;
use App\Filament\Resources\Staff\StaffResource;
use App\Models\Chair;
use App\Models\Group;
use App\Models\StaffAdjustment;
use App\Models\StaffAppointment;
use App\Models\StaffContract;
use App\Models\StaffEntryEducation;
use App\Models\StaffStatus;
use App\Models\StaffWorkEducation;
use App\Models\StaffWorkExperience;
use App\Models\Unit;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

    protected static ?string $title = 'Ubah Data Pegawai';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $staff = $this->record;

        if ($staff->contract) {
            $data['contract'] = $staff->contract->toArray();
        }

        if ($staff->appointment) {
            $data['appointment'] = $staff->appointment->toArray();
        }

        if ($staff->adjustment) {
            $data['adjustment'] = $staff->adjustment->toArray();
        }

        if ($staff->entryEducation) {
            $data['has_entry_education'] = true;
            $data['entryEducation'] = $staff->entryEducation->toArray();
        }

        if ($staff->workEducation) {
            $data['has_work_education'] = true;
            $data['workEducation'] = $staff->workEducation->toArray();
        }

        if ($staff->workExperience) {
            $data['has_work_experience'] = true;
            $data['workExperience'] = $staff->workExperience->toArray();
        }

        return $data;
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label('Simpan') 
            ->action(fn () => $this->save())
            ->requiresConfirmation(function ($record) {
                $formData = $this->data;
                $explicitFields = ['staff_status_id', 'chair_id', 'unit_id', 'group_id'];
                
                foreach ($explicitFields as $field) {
                    $newVal = $formData[$field] ?? null;
                    if ($newVal != $record->{$field}) {
                        return true;
                    }
                }

                $lastHistory = $record->workHistories()->latest('id')->first();
                $lastDecreeNumber = $lastHistory?->decree_number; 

                $currentStatus = $formData['staff_status_id'] ?? null;
                $inputNumber = null;

                if ($currentStatus == 1) { 
                    if (isset($formData['adjustment']) && is_array($formData['adjustment'])) {
                        $inputNumber = $formData['adjustment']['decree_number'] ?? null;
                    } else {
                        $inputNumber = $formData['decree_number'] ?? null;
                    }
                } 
                elseif ($currentStatus == 2) {
                    if (isset($formData['contract']) && is_array($formData['contract'])) {
                        $inputNumber = $formData['contract']['contract_number'] ?? null;
                    } else {
                        $inputNumber = $formData['contract_number'] ?? null;
                    }
                }

                if ($inputNumber && $inputNumber !== $lastDecreeNumber) {
                    return true;
                }
                
                return false;
            })
            ->modalHeading('Konfirmasi Pembaruan Data')
            ->modalDescription(function ($record) {
                $formData = $this->data;
                $lines = [];
                $lines[] = "Sistem mendeteksi perubahan pada <strong>Data Sensitif</strong>:";

                $newStatusId = $formData['staff_status_id'] ?? null;
                if ($newStatusId != $record->staff_status_id) {
                    $statusName = StaffStatus::find($newStatusId)?->name ?? 'Tidak Diketahui';
                    $lines[] = "- Status Kepegawaian: " . $statusName;
                } 

                $newChairId = $formData['chair_id'] ?? null;
                if ($newChairId != $record->chair_id) {
                    $chairName = Chair::find($newChairId)?->name ?? 'Tidak Diketahui';
                    $lines[] = "- Jabatan: " . $chairName;
                } 

                $newUnitId = $formData['unit_id'] ?? null;
                if ($newUnitId != $record->unit_id) {
                    $unitName = Unit::find($newUnitId)?->name ?? 'Tidak Diketahui';
                    $lines[] = "- Unit: " . $unitName;
                } 

                $newGroupId = $formData['group_id'] ?? null;
                if ($newGroupId != $record->group_id){
                    $groupName = Group::find($newGroupId)?->name ?? 'Tidak Diketahui';
                    $lines[] = "- Kelompok Tenaga Kerja: " . $groupName;
                }

                $lastHistory = $record->workHistories()->latest('id')->first();
                $lastDecreeNumber = $lastHistory?->decree_number;

                $inputContract = $formData['contract']['contract_number'] ?? null;
                $inputAdjustment = $formData['adjustment']['decree_number'] ?? null;

                if ($inputContract && $inputContract !== $lastDecreeNumber) {
                    $lines[] = "- Nomor SK Kontrak: " . $inputContract;
                } elseif ($inputAdjustment && $inputAdjustment !== $lastDecreeNumber) {
                    $lines[] = "- Nomor SK Tetap: " . $inputAdjustment;
                }

                $content = implode('<br>', $lines);

                $footer = "<br><br>Tindakan ini akan otomatis MENUTUP history jabatan lama dan MEMBUAT HISTORY BARU. <br>Apakah Anda yakin data ini benar?";

                return new HtmlString($content . $footer);
            })
            ->modalSubmitActionLabel('Ya, Perbarui Histori')
            ->modalIcon('heroicon-o-exclamation-triangle');
    }

    protected function afterSave(): void
    {
        $row = $this->record;
        $data = $this->data;

        if (!empty($data['contract']['contract_number'])) {
            StaffContract::updateOrCreate(
                ['staff_id' => $row->id],
                [
                    'contract_number' => $data['contract']['contract_number'],
                    'decree' => collect($data['contract']['decree'])->first() ?? null,
                    'start_date' => $data['contract']['start_date'] ?? null,
                    'end_date' => $data['contract']['end_date'] ?? null,
                ]
            );
        }

        if (!empty($data['appointment']['decree_number'])) {
            StaffAppointment::updateOrCreate(
                ['staff_id' => $row->id],
                [
                    'decree_number' => $data['appointment']['decree_number'],
                    'decree_date' => $data['appointment']['decree_date'] ?? null,
                    'decree' => collect($data['appointment']['decree'])->first() ?? null,
                    'class' => $data['appointment']['class'] ?? null,
                ]
            );
        }

        if (!empty($data['adjustment']['decree_number'])) {
            StaffAdjustment::updateOrCreate(
                ['staff_id' => $row->id],
                [
                    'decree_number' => $data['adjustment']['decree_number'],
                    'decree_date' => $data['adjustment']['decree_date'] ?? null,
                    'decree' => collect($data['adjustment']['decree'])->first() ?? null,
                    'class' => $data['adjustment']['class'] ?? null,
                ]
            );
        }

        if (!empty($data['entryEducation']['level'])) {
            StaffEntryEducation::updateOrCreate(
                ['staff_id' => $row->id],
                [
                'level' => $data['entryEducation']['level'],
                'institution' => $data['entryEducation']['institution'] ?? null,
                'certificate_number' => $data['entryEducation']['certificate_number'] ?? null,
                'certificate_date' => $data['entryEducation']['certificate_date'] ?? null,
                'certificate' => collect($data['entryEducation']['certificate'])->first() ?? null,
                'nonformal_education' => $data['entryEducation']['nonformal_education'] ?? null,
                'adverb' => $data['entryEducation']['adverb']?? null,
            ]);
        }

        if (!empty($data['workEducation']['level'])) {
            StaffWorkEducation::updateOrCreate(
                ['staff_id' => $row->id],
                [
                'level' => $data['workEducation']['level'],
                'major' => $data['workEducation']['major'] ?? null,
                'institution' => $data['workEducation']['institution'] ?? null,
                'certificate_number' => $data['workEducation']['certificate_number'] ?? null,
                'certificate_date' => $data['workEducation']['certificate_date'] ?? null,
                'certificate' => collect($data['workEducation']['certificate'])->first() ?? null,
            ]);
        }

        if (!empty($data['workExperience']['institution'])) {
            StaffWorkExperience::updateOrCreate(
                ['staff_id' => $row->id],
                [
                'institution' => $data['workExperience']['institution'],
                'work_length' => $data['workExperience']['work_length'] ?? null,
                'certificate' => collect($data['workExperience']['certificate'])->first() ?? null,
                'admission' => $data['workExperience']['admission'] ?? null,
            ]);
        }

        Notification::make()
            ->title('Pembaruan Data Diri')
            ->body('Data Diri Anda telah mendapat pembaruan dari SDM!')
            ->success()
            ->actions([
                Action::make('read')
                    ->label('Lihat')
                    ->button()
                    ->url(ProfileResource::getUrl('index'))
                    ->markAsRead()
            ])
            ->sendToDatabase($this->record->user);
    }

}
