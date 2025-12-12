<?php

namespace App\Filament\Resources\StaffAdministrations\Pages;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use App\Models\Staff;
use Filament\Actions\Action;
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
                    route('filament.admin.resources.staff-administrations.view', $staff)
                );
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mine')
                ->label('Administrasi Saya')
                ->icon('heroicon-m-wallet')
                ->color('warning')
                ->visible(fn() => Auth::user()->role_id == 1)
                ->url(StaffAdministrationResource::getUrl('view', ['record' => Auth::user()->staff_id]))
        ];
    }
}
