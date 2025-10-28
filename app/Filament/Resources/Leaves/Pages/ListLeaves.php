<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Resources\Leaves\LeaveResource;
use App\Filament\Resources\Leaves\Tables\ApproveTable;
use App\Filament\Resources\Leaves\Tables\LeavesTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
        return [
            'pengajuan' => Tab::make('Pengajuan')
                ->icon('heroicon-o-document-text'),

            'persetujuan' => Tab::make('Persetujuan')
                ->icon('heroicon-o-clipboard-document-check'),
        ];
    }

    // Bedain query masing-masing tab/table
    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $activeTab = $this->activeTab ?? null;

        if ($activeTab === 'pengajuan') {
            $query->where('staff_id', Auth::user()->staff_id)
                ->orderBy('start_date', 'DESC');
        }

        if ($activeTab === 'persetujuan') {
            $query->whereHas('staff.unit', function ($q) {
                $q->where('leader_id', Auth::user()->staff_id)
                    ->orderBy('start_date', 'DESC');;
            });
        }

        return $query;
    }

    // Atur view dari tab dengan ambil table
    public function getTable(): Table
    {
        $activeTab = $this->activeTab ?? 'pengajuan';
        $table = parent::getTable();

        if ($activeTab === 'pengajuan') {
            return LeavesTable::configure($table);
        }

        if ($activeTab === 'persetujuan') {
            return ApproveTable::configure($table);
        }

        return $table;
    }

}
