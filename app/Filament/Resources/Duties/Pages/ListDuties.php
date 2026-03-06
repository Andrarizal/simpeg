<?php

namespace App\Filament\Resources\Duties\Pages;

use App\Filament\Resources\Duties\DutyResource;
use App\Filament\Resources\Duties\Tables\DutiesTable;
use App\Filament\Resources\Duties\Tables\StaffsTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ListDuties extends ListRecords
{
    protected static string $resource = DutyResource::class;

    public ?string $pdfToken = null;
    
    public function mount(): void
    {
        $requestedTab = request()->query('tab') ?? request()->query('activeTab');

        if ($requestedTab) {
            $this->activeTab = $requestedTab;
        }

        if ($requestedTab === 'verifikasi') {
            $user = Auth::user();
            $isAdmin = $user->role_id != 4;

            if (! $isAdmin) {
                $this->redirect($this->getResource()::getUrl('index'));
                return;
            }
        }

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Buat Surat Tugas')
            ->visible(fn () => str_contains(Auth::user()->staff->chair->name, 'Sekretariat')),
        ];
    }

    public function getTabs(): array
    {
        $user = Auth::user();
        $user->staff_id = $user->staff_id ?? 1;

        $arrOfTabs = [];
        
        if ($user->staff->chair->level == 4 && $user->role_id == 1){
            $arrOfTabs['tugas'] = Tab::make('Tugas Anda')
                ->icon('heroicon-o-document-text');
            $arrOfTabs['verifikasi'] = Tab::make('Perlu Verifikasi')
                ->icon('heroicon-o-clipboard-document-check');
        }

        return $arrOfTabs;
    }

    public function getTable(): Table
    {
        $this->activeTab = $this->activeTab ?? 'tugas';
        if (Auth::user()->staff->chair->level < 4){
            $this->activeTab = "verifikasi";
        }
        $table = parent::getTable();

        if ($this->activeTab == 'tugas') {
            return DutiesTable::configure($table);
        }

        if ($this->activeTab == 'verifikasi') {
            return StaffsTable::configure($table);
        }

        return $table;
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
