<?php

namespace App\Filament\Pages;

use App\Models\Chair;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class OrganizationStructure extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::RectangleGroup;
    
    protected static ?string $navigationLabel = 'Struktur Organisasi';

    protected static ?string $title = 'Bagan Struktur Organisasi';
    
    protected string $view = 'filament.pages.organization-structure';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_pdf')
                ->label('Cetak / Simpan PDF')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->action(function () {
                    $this->js('window.print()');
                }),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'rootChairs' => Chair::whereNull('head_id')
                ->with([
                    // PENTING: Load 'ledUnit' pada children agar filter Model tahu dia pemimpin atau bukan
                    'children.ledUnit', 
                    'children.unit', 
                    
                    'ledUnit' // Untuk root (opsional tapi bagus)
                ]) 
                ->get(),
        ];
    }
}
