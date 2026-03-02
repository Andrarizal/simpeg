<?php

namespace App\Filament\Resources\Duties\Pages;

use App\Filament\Resources\Duties\DutyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListDuties extends ListRecords
{
    protected static string $resource = DutyResource::class;

    public ?string $pdfToken = null;
    
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Buat Surat Tugas')
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
