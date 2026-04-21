<?php

namespace App\Filament\Resources\Letters\Pages;

use App\Filament\Resources\Letters\LetterResource;
use App\Models\Staff;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLetter extends CreateRecord
{
    protected static string $resource = LetterResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        $staff = Auth::user()->staff;

        $recipients = $record->targetStaffs->map(fn ($staff) => $staff->user)->filter();

        if ($record->classification == 'Disposisi') {
            $recipients = Staff::with('user')->whereHas('chair', function ($query) {
                $query->where('name', 'like', '%Umum & Kepegawaian%');
            })->get()->pluck('user')->filter();

            Notification::make()
                ->title('Disposisi Baru perlu ditindaklanjuti')
                ->icon('heroicon-o-document-text')
                ->body("Disposisi baru perlu ditindaklanjuti.\nPerihal: {$record->title}")
                ->actions([
                    Action::make('lihat')
                        ->label('Lihat')
                        ->url(LetterResource::getUrl('index')) 
                        ->button()
                        ->markAsRead(),
                ])
                ->sendToDatabase($recipients);
        } else {
            Notification::make()
                ->title('Undangan Baru')
                ->icon('heroicon-o-document-text')
                ->body("Anda menerima undangan baru.\nPerihal: {$record->title}")
                ->actions([
                    Action::make('lihat')
                        ->label('Lihat')
                        ->url(LetterResource::getUrl('index')) 
                        ->button()
                        ->markAsRead(),
                ])
                ->sendToDatabase($recipients);
        }
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
