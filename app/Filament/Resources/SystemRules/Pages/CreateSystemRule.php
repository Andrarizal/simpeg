<?php

namespace App\Filament\Resources\SystemRules\Pages;

use App\Filament\Resources\SystemRules\SystemRuleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSystemRule extends CreateRecord
{
    protected static string $resource = SystemRuleResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();
        if (!$user || !$user->staff || !$user->staff->chair) {
            return false; 
        }

        return $user->role_id == 1;
    }
}
