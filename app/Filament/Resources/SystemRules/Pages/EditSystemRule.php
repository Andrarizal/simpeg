<?php

namespace App\Filament\Resources\SystemRules\Pages;

use App\Filament\Resources\SystemRules\SystemRuleResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditSystemRule extends EditRecord
{
    protected static string $resource = SystemRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil disimpan')
            ->body('Perubahan telah disimpan. Klik tombol di bawah untuk menerapkan konfigurasi.')
            ->duration(10000)
            ->actions([
                Action::make('clear_config')
                    ->label('Terapkan Konfigurasi')
                    ->button()
                    ->color('warning')
                    ->icon('heroicon-m-arrow-path')
                    ->url('/clear-config'),
            ]);
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
