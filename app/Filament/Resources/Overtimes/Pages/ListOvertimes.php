<?php

namespace App\Filament\Resources\Overtimes\Pages;

use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Filament\Resources\Overtimes\Tables\ApproveTable;
use App\Filament\Resources\Overtimes\Tables\OvertimesTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ListOvertimes extends ListRecords
{
    protected static string $resource = OvertimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajukan Lembur'),
        ];
    }

    public function getTabs(): array
    {
        $user = Auth::user();
        $user->staff_id = $user->staff_id ?? 1;

        $arrOfTabs = [];
        
        if ($user->staff->chair->level == 4 && $user->staff->unit->leader_id == $user->staff->chair_id){
            $arrOfTabs['pengajuan'] = Tab::make('Pengajuan Anda')->icon('heroicon-o-document-text');
            $arrOfTabs['persetujuan'] = Tab::make('Perlu Persetujuan')->icon('heroicon-o-clipboard-document-check');
        } else if ($user->staff->chair->level == 3){
            $arrOfTabs['persetujuan'] = Tab::make('Perlu Persetujuan')->icon('heroicon-o-clipboard-document-check');
        }

        return $arrOfTabs;
    }

    public function getTable(): Table
    {
        $activeTab = $this->activeTab ?? 'pengajuan';
        $table = parent::getTable();

        if ($activeTab === 'pengajuan') {
            return OvertimesTable::configure($table);
        }

        if ($activeTab === 'persetujuan') {
            return ApproveTable::configure($table);
        }

        return $table;
    }
}
