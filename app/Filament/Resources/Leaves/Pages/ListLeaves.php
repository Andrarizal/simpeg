<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Resources\Leaves\LeaveResource;
use App\Filament\Resources\Leaves\Tables\ApproveTable;
use App\Filament\Resources\Leaves\Tables\LeavesTable;
use App\Filament\Resources\Leaves\Tables\ReplacerTable;
use App\Models\Leave;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    public function mount(): void
    {
        $requestedTab = request()->query('tab') ?? request()->query('activeTab');

        if ($requestedTab === 'persetujuan') {
            $user = Auth::user();
            $isBoss = $user->staff->chair->level != 4 || $user->staff->unit->leader_id == $user->staff->chair_id;

            if (! $isBoss) {
                $this->redirect($this->getResource()::getUrl('index'));
                return;
            }
        }

        Leave::query()
            ->whereDate('start_date', '<', Carbon::now()->toDateString())
            ->where(function ($query) {
                $query->where('status', '!=', 'Disetujui Kepala Seksi')
                      ->orWhere('status', '!=', 'Disetujui Direktur')
                      ->orWhereNull('is_verified');
            })->delete();
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajukan Cuti / Izin')
                ->hidden(fn () => Auth::user()->staff->chair->level == 1),
        ];
    }
 
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
