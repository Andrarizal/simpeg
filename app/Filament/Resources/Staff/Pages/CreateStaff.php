<?php

namespace App\Filament\Resources\Staff\Pages;

use App\Filament\Resources\Staff\StaffResource;
use App\Models\Chair;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected static ?string $title = 'Daftarkan Pegawai';

    protected function afterCreate(): void {
        $row = $this->record;

        $role = Chair::where('id', $row->chair_id)->first();

        $confirmation = $this->data['confirmation'] ?? false;

        if ($confirmation){
            User::create([
                'name' => $row->name,
                'email' => $row->personal_email,
                'password' => bcrypt(date('dmY', strtotime($row->birth_date))),
                'role_id' => $role->level + 1,
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
