<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Resources\Leaves\LeaveResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;

    protected ?string $heading = 'Ajukan Cuti/Izin';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Ajukan')
                ->color('primary')
                ->action('create') 
                ->keyBindings(['enter']),
            parent::getCancelFormAction()
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $user = Auth::user();
        $staff = $user->staff;
        $receiver = null;

        if ($staff->unit->work_system == 'Shift'){
            $receiver = $record->replacement;
        } else {
            if ($staff->chair->level == 4){
                $receiver = $staff->unit->leader ?? $staff->chair->head;
            } else {
                $receiver = $staff->chair->head;
            }
        }

        if ($receiver && $receiver->id != $staff->id){
            $recipient = $receiver->user; 

            if ($recipient) {
                Notification::make()
                    ->title("Permintaan menggantikan {$record->type}")
                    ->body("{$staff->name} telah mengajukan {$record->type} dengan menunjuk Anda sebagai pengganti untuk tanggal " . Carbon::parse($record->start_date)->translatedFormat('d F Y'))
                    ->warning()
                    ->icon('heroicon-o-document-text')
                    ->iconColor('warning')
                    ->actions([
                        Action::make('review')
                            ->label('Tinjau')
                            ->url(LeaveResource::getUrl('view', ['record' => $record]))
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($recipient);
            }
        }
        $this->redirect(LeaveResource::getUrl());
    }
}
