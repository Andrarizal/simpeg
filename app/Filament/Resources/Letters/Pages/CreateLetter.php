<?php

namespace App\Filament\Resources\Letters\Pages;

use App\Filament\Resources\Letters\LetterResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLetter extends CreateRecord
{
    protected static string $resource = LetterResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        $recipients = $record->targetStaffs->map(fn ($staff) => $staff->user)->filter();

        Notification::make()
            ->title('Surat Masuk Baru')
            ->icon('heroicon-o-document-text')
            ->body("Anda menerima surat masuk baru.\nPerihal: {$record->title}")
            ->actions([
                Action::make('lihat')
                    ->label('Lihat Dokumen')
                    ->url(LetterResource::getUrl('index')) 
                    ->button()
                    ->markAsRead(),
            ])
            ->sendToDatabase($recipients);
    }
}
