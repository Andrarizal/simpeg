<?php

namespace App\Filament\Resources\ContractEvaluations\Pages;

use App\Filament\Resources\ContractEvaluations\ContractEvaluationResource;
use Filament\Resources\Pages\ManageRecords;

class ManageContractEvaluations extends ManageRecords
{
    protected static string $resource = ContractEvaluationResource::class;

    public ?string $pdfToken = null;

    protected function getHeaderActions(): array
    {
        return [
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
