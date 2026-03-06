<?php

namespace App\Filament\Resources\OnCalls\Pages;

use App\Filament\Resources\OnCalls\OnCallResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageOnCalls extends ManageRecords
{
    protected static string $resource = OnCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Perintahkan On Call')
                ->visible(fn () => !OnCallResource::isSubordinate()),
            Action::make('periods')
                ->label('Kelola Periode')
                ->modalHeading('Manajemen Periode Bulanan')
                ->modalContent(view('filament.pages.partials.monthly-period-manager-modal')) 
                ->modalSubmitAction(false) 
                ->modalCancelAction(false)
                ->modalWidth('xl')
                ->icon('heroicon-o-swatch')
                ->color('gray')
                ->visible(fn() => Auth::user()->role_id == 1)
                ->slideOver(),
        ];
    }
}
