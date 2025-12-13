<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Resources\Leaves\LeaveResource;
use App\Filament\Resources\Leaves\Tables\ApproveTable;
use App\Filament\Resources\Leaves\Tables\LeavesTable;
use App\Filament\Resources\Leaves\Tables\ReplacerTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajukan Cuti / Izin'),
        ];
    }
 
    // Bikin dua tabs
    public function getTabs(): array
    {
        $user = Auth::user();
        $user->staff_id = $user->staff_id ?? 1;

        $arrOfTabs = [];
        
        if ($user->staff->chair->level != 1){
            $arrOfTabs['pengajuan'] = Tab::make('Pengajuan Anda')->icon('heroicon-o-document-text');
            $arrOfTabs['pengganti'] = Tab::make('Pengganti')->icon('heroicon-o-document-arrow-up');
        }

        if ($user->staff->chair->level != 4 || $user->role_id == 1 || $user->staff->unit->leader_id == $user->staff->chair_id){
            $arrOfTabs['persetujuan'] = Tab::make($user->role_id == 1 ? 'Perlu Verifikasi' : 'Perlu Persetujuan')->icon('heroicon-o-clipboard-document-check');
        }

        return $arrOfTabs;
    }

    // Atur view dari tab dengan ambil table
    public function getTable(): Table
    {
        $activeTab = $this->activeTab ?? 'pengajuan';
        $table = parent::getTable();

        if ($activeTab == 'pengajuan') {
            return LeavesTable::configure($table);
        }

        if ($activeTab == 'pengganti') {
            return ReplacerTable::configure($table);
        }

        if ($activeTab == 'persetujuan') {
            return ApproveTable::configure($table);
        }

        return $table;
    }

}
