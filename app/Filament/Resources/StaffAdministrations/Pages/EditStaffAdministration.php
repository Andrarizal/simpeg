<?php

namespace App\Filament\Resources\StaffAdministrations\Pages;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use App\Models\Staff;
use App\Models\StaffAdministration;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class EditStaffAdministration extends EditRecord
{
    protected static string $resource = StaffAdministrationResource::class;
    protected static ?string $title = 'Perbarui Administrasi Pegawai';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->action(fn () => $this->save())
                ->keyBindings(['mod+s'])
                ->requiresConfirmation()
                ->modalHeading('Perbarui Data Administrasi?')
                ->modalDescription('Perhatian: Mengubah data atau mengunggah dokumen baru akan MERESET status verifikasi menjadi "Belum Diverifikasi".')
                ->modalSubmitActionLabel('Ya, Simpan & Reset')
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->color('primary'),
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

    public function getSubheading(): string|Htmlable|null
    {
        $staff = Staff::where('id', $this->record->id)->first();

        $nameStaff = "
            <div class='flex items-center gap-1 whitespace-nowrap bg-gray-100 dark:bg-white/5 px-2 py-1 rounded-md border border-gray-200 dark:border-white/10'>
                <span class='font-bold text-primary-600 dark:text-primary-400'>Nama:</span>
                <span class='text-gray-700 dark:text-gray-300'> $staff->name</span>
            </div>
        ";

        return new HtmlString("
            <div class='flex flex-wrap items-center gap-2 mt-2 text-xs'>
                <div class='flex items-center justify-center w-6 h-6 bg-gray-100 dark:bg-gray-800 rounded-full shrink-0'>
                    <svg class='w-4 h-4 text-gray-500' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'>
                        <path fill-rule='evenodd' clip-rule='evenodd' d='M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z' />
                    </svg>
                </div>
                
                {$nameStaff}
            </div>
        ");
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['is_verified'] = null;
        return $data;
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
