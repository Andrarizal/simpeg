<?php

namespace App\Filament\Resources\Letters\Pages;

use App\Filament\Resources\Letters\LetterResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditLetter extends EditRecord
{
    protected static string $resource = LetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $staff = Auth::user()->staff;

        $recipients = $record->targetStaffs->map(fn ($staff) => $staff->user)->filter();

        Notification::make()
            ->title('Disposisi Baru')
            ->icon('heroicon-o-document-text')
            ->body("Anda menerima disposisi baru.\nPerihal: {$record->title}")
            ->actions([
                Action::make('lihat')
                    ->label('Lihat')
                    ->url(LetterResource::getUrl('index')) 
                    ->button()
                    ->markAsRead(),
            ])
            ->sendToDatabase($recipients);

        Notification::make()
            ->title('Disposisi Ditindaklanjuti')
            ->icon('heroicon-o-document-text')
            ->body("Disposisi dengan perihal '{$record->title}' telah ditindaklanjuti.")
            ->send();
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();
        if (!$user || !$user->staff || !$user->staff->chair) {
            return false; 
        }

        return $user->role_id == 1 || str_contains($user->staff->chair->name, 'Umum & Kepegawaian');
    }
}
