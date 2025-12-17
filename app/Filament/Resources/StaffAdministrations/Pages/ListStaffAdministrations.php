<?php

namespace App\Filament\Resources\StaffAdministrations\Pages;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use App\Models\Staff;
use App\Models\StaffAdministration;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListStaffAdministrations extends ListRecords
{
    protected static string $resource = StaffAdministrationResource::class;

    public function mount(): void
    {
        parent::mount();

        if (Auth::user()->role_id == 2) {
            $user = Auth::user();
            
            $administration = StaffAdministration::where('staff_id', $user->staff_id)->first();

            if ($administration) {
                $this->redirect(
                    StaffAdministrationResource::getUrl('view', ['record' => $administration])
                );
            } else {
                Notification::make()->title('Data administrasi belum dibuat oleh Admin.')->warning()->send();
                $this->redirect('/admin'); 
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
