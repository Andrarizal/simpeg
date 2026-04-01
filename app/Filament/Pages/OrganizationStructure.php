<?php

namespace App\Filament\Pages;

use App\Models\Chair;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class OrganizationStructure extends Page
{
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::RectangleGroup;
    
    protected static ?string $navigationLabel = 'Struktur Organisasi';

    protected static ?string $title = 'Bagan Struktur Organisasi';
    
    protected string $view = 'filament.pages.organization-structure';

    public static function getNavigationGroup(): ?string
    {
        if (Auth::user()->staff->chair->level == 3 && Auth::user()->role_id == 1) {
            return 'Perusahaan'; 
        }
        return null;
    }

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
                    'children.ledUnit', 
                    'children.unit', 
                    'ledUnit'
                ]) 
                ->get(),
        ];
    }
}
