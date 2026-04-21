<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
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
