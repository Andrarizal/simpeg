<?php

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Resources\Profiles\ProfileResource;
use App\Models\StaffAdjustment;
use App\Models\StaffAppointment;
use App\Models\StaffContract;
use App\Models\StaffEntryEducation;
use App\Models\StaffWorkEducation;
use App\Models\StaffWorkExperience;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;
    protected static ?string $title = 'Profil Pegawai';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->label('Ganti Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->modalHeading('Ganti Password')
                ->modalDescription('Silakan masukkan password lama dan password baru Anda.')
                ->modalSubmitActionLabel('Simpan Password Baru')
                ->modalWidth('md')
                ->schema([
                    TextInput::make('current_password')
                        ->label('Password Lama')
                        ->password()
                        ->required()
                        ->revealable()
                        ->currentPassword()
                        ->validationMessages([
                            'current_password' => 'Password lama yang Anda masukkan tidak benar.',
                        ]),

                    TextInput::make('new_password')
                        ->label('Password Baru')
                        ->password()
                        ->required()
                        ->revealable()
                        ->minLength(6) 
                        ->different('current_password')
                        ->same('new_password_confirmation')
                        ->validationMessages([
                            'different' => 'Password baru tidak boleh sama dengan password lama.',
                            'same' => 'Password baru dan konfirmasi tidak cocok.',
                            'min' => 'Password minimal harus 6 karakter.'
                        ]),

                    TextInput::make('new_password_confirmation')
                        ->label('Konfirmasi Password Baru')
                        ->password()
                        ->revealable()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $user = Auth::user()->staff->user;

                    $user->update([
                        'password' => Hash::make($data['new_password']),
                    ]);

                    Notification::make()
                        ->title('Berhasil!')
                        ->body('Password Anda telah berhasil diubah.')
                        ->success()
                        ->send();
                }),
            Action::make('my_history')
                ->label('Riwayat Jabatan Saya')
                ->color('info')
                ->url(route('filament.admin.resources.staff.history', ['record' => Auth::user()->staff_id])),
            Action::make('save')
                ->label('Simpan')
                ->action(fn () => $this->save())
        ];
    }

    public function mount($record = null): void
    {
        $staff = Auth::user()->staff;

        if (!$staff) {
            Notification::make()->title('Akun Anda belum terhubung dengan data pegawai.')->danger()->send();
            $this->redirect('/dashboard');
            return;
        }

        parent::mount($staff->getKey());
    }

    protected function resolveRecord($key): Model
    {
        $staff = Auth::user()->staff;
        
        if ($staff->id != $key) {
            return $staff; 
        }
        
        return parent::resolveRecord($key);
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
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
