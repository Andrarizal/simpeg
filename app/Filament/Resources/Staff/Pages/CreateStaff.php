<?php

namespace App\Filament\Resources\Staff\Pages;

use App\Filament\Resources\Staff\StaffResource;
use App\Models\Chair;
use App\Models\StaffAdjustment;
use App\Models\StaffAppointment;
use App\Models\StaffContract;
use App\Models\StaffEntryEducation;
use App\Models\StaffWorkEducation;
use App\Models\StaffWorkExperience;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected static ?string $title = 'Daftarkan Pegawai';

    protected function afterCreate(): void {
        $row = $this->record;
        $data = $this->data;

        // Masukkan data dependensi lain apabila ada
        if (!empty($data['contract']['contract_number'])) {
            StaffContract::create([
                'staff_id' => $row->id,
                'contract_number' => $data['contract']['contract_number'],
                'decree' => collect($data['contract']['decree'])->first() ?? null,
                'start_date' => $data['contract']['start_date'] ?? null,
                'end_date' => $data['contract']['end_date'] ?? null,
            ]);
        }

        if (!empty($data['appointment']['decree_number'])) {
            StaffAppointment::create([
                'staff_id' => $row->id,
                'decree_number' => $data['appointment']['decree_number'],
                'decree_date' => $data['appointment']['decree_date'] ?? null,
                'decree' => collect($data['appointment']['decree'])->first() ?? null,
                'class' => $data['appointment']['class'] ?? null,
            ]);
        }

        if (!empty($data['adjustment']['decree_number'])) {
            StaffAdjustment::create([
                'staff_id' => $row->id,
                'decree_number' => $data['adjustment']['decree_number'],
                'decree_date' => $data['adjustment']['decree_date'] ?? null,
                'decree' => collect($data['adjustment']['decree'])->first() ?? null,
                'class' => $data['adjustment']['class'] ?? null,
            ]);
        }

        if (!empty($data['entryEducation']['level'])) {
            StaffEntryEducation::create([
                'staff_id' => $row->id,
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
            StaffWorkEducation::create([
                'staff_id' => $row->id,
                'level' => $data['workEducation']['level'],
                'major' => $data['workEducation']['major'] ?? null,
                'institution' => $data['workEducation']['institution'] ?? null,
                'certificate_number' => $data['workEducation']['certificate_number'] ?? null,
                'certificate_date' => $data['workEducation']['certificate_date'] ?? null,
                'certificate' => collect($data['workEducation']['certificate'])->first() ?? null,
            ]);
        }

        if (!empty($data['workExperience']['institution'])) {
            StaffWorkExperience::create([
                'staff_id' => $row->id,
                'institution' => $data['workExperience']['institution'],
                'work_length' => $data['workExperience']['work_length'] ?? null,
                'certificate' => collect($data['workExperience']['certificate'])->first() ?? null,
                'admission' => $data['workExperience']['admission'] ?? null,
            ]);
        }

        $level = Chair::where('id', $row->chair_id)->first();
        $role = $level->level == 4 ? 2 : 1;

        // Buatkan User dengan Record
        $confirmation = $this->data['confirmation'] ?? false;
        if ($confirmation){
            User::create([
                'name' => $row->name,
                'email' => $row->email,
                'password' => bcrypt(date('dmY', strtotime($row->birth_date))),
                'role_id' => $role,
                'staff_id' => $row->id
            ]);

            Notification::make()
                ->title('Akun pengguna berhasil dibuat')
                ->body("Akun untuk {$row->name} telah dibuat")
                ->success()
                ->send();
        }
    }
}
