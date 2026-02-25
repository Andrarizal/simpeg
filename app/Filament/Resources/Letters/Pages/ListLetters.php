<?php

namespace App\Filament\Resources\Letters\Pages;

use App\Filament\Resources\Letters\LetterResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListLetters extends ListRecords
{
    protected static string $resource = LetterResource::class;

    public ?string $pdfToken = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('template-manager')
                ->label('Kelola Template Surat')
                ->modalHeading('Manajemen Template Surat')
                ->modalContent(view('filament.pages.partials.template-manager-modal')) 
                ->modalSubmitAction(false) 
                ->modalCancelAction(false)
                ->modalWidth('3xl')
                ->icon('heroicon-o-swatch')
                ->color('gray')
                ->slideOver(),
            CreateAction::make()
                ->label('Keluarkan Surat')
                ->visible(fn () => str_contains(Auth::user()->staff->chair->name, 'Sekretariat')),
        ];
    }

    public function closePreviewAndCleanup() {
        if ($this->pdfToken) {
            $path = storage_path("app/private/livewire-tmp/{$this->pdfToken}.pdf");
            if (file_exists($path)) {
                @unlink($path);
            }
            $this->pdfToken = null;
        }

        $this->unmountAction();
    }
}
