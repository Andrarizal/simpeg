<?php

namespace App\Filament\Resources\Staff\Pages;

use App\Filament\Resources\Staff\StaffResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

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

        // --- KONTRAK ---
        if ($staff->contract) {
            $data['contract'] = $staff->contract->toArray();
        }

        // --- PENGANGKATAN ---
        if ($staff->appointment) {
            $data['appointment'] = $staff->appointment->toArray();
        }

        // --- PENYESUAIAN ---
        if ($staff->adjustment) {
            $data['adjustment'] = $staff->adjustment->toArray();
        }

        // --- PENDIDIKAN AWAL ---
        if ($staff->entryEducation) {
            $data['has_entry_education'] = true;
            $data['entryEducation'] = $staff->entryEducation->toArray();
        }

        // --- PENDIDIKAN KERJA ---
        if ($staff->workEducation) {
            $data['has_work_education'] = true;
            $data['workEducation'] = $staff->workEducation->toArray();
        }

        // --- PENGALAMAN KERJA ---
        if ($staff->workExperience) {
            $data['has_work_experience'] = true;
            $data['workExperience'] = $staff->workExperience->toArray();
        }

        return $data;
    }
}
