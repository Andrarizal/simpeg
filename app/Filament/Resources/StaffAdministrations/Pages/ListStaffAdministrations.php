<?php

namespace App\Filament\Resources\StaffAdministrations\Pages;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use App\Models\Staff;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListStaffAdministrations extends ListRecords
{
    protected static string $resource = StaffAdministrationResource::class;

    public function mount(): void
    {
        parent::mount();

        // Cek role user (atau kondisi lain)
        if (Auth::user()->role_id == 2) {
            // Redirect langsung ke edit halaman profil user sendiri
            $staff = Staff::where('id', Auth::user()->staff_id)->first();

            if ($staff) {
                $this->redirect(
                    route('filament.admin.resources.staff-administrations.edit', $staff)
                );
            }
        }
    }
}
