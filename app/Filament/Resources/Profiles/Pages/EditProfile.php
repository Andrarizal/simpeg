<?php

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;
    protected static ?string $title = 'Profil Pegawai';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                ->action(fn () => $this->save())
        ];
    }

    public function mount($record = null): void
    {
        // Ambil pegawai yang sedang login
        $staff = Auth::user()->staff ?? Auth::user();

        // Jalankan mount parent dengan ID pegawai
        parent::mount($staff->getKey());
    }

    protected function getRedirectUrl(): string
    {
        // Setelah disimpan, tetap di halaman profil
        return static::getResource()::getUrl('index');
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

        if ($staff->training) {
            $data['training'] = $staff->training->toArray();
        }

        return $data;
    }
}
