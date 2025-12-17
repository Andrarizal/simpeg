<?php

namespace App\Filament\Resources\StaffAdministrations\Pages;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use App\Models\StaffAdministration;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditStaffAdministration extends EditRecord
{
    protected static string $resource = StaffAdministrationResource::class;
    protected static ?string $title = 'Perbarui Administrasi Pegawai';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    public function mount($record): void
    {
        if (Auth::user()->role_id == 2) {
            $recordModel = StaffAdministration::findOrFail($record);
            
            if ($recordModel->staff_id != Auth::user()->staff_id) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }
        }

        parent::mount($record);
    }
}
