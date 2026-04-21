<?php

namespace App\Filament\Resources\Duties\Pages;

use App\Filament\Resources\Duties\DutyResource;
use App\Filament\Resources\Letters\LetterResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDuty extends CreateRecord
{
    protected static string $resource = DutyResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        $recipients = $record->targetStaffs->map(fn ($staff) => $staff->user)->filter();

        Notification::make()
            ->title('Surat Tugas Baru')
            ->icon('heroicon-o-document-text')
            ->body("Anda menerima surat tugas baru.\nPerihal: {$record->title}")
            ->actions([
                Action::make('lihat')
                    ->label('Lihat Dokumen')
                    ->url(LetterResource::getUrl('index')) 
                    ->button()
                    ->markAsRead(),
            ])
            ->sendToDatabase($recipients);
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();
        if (!$user || !$user->staff || !$user->staff->chair) {
            return false; 
        }

        return $user->role_id == 1;
    }
}
