<?php

namespace App\Filament\Resources\Duties\Tables;

use App\Filament\Resources\Duties\DutyResource;
use App\Models\Staff;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StaffsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = Staff::query();
                $pivotTable = 'duty_receivers';

                $query->leftJoin('chairs', 'staff.chair_id', '=', 'chairs.id')
                    ->where('chairs.level', '=', 4);
                $query->leftJoin($pivotTable, function ($join) use ($pivotTable) {
                    $join->on($pivotTable . '.staff_id', '=', 'staff.id')
                        ->whereNotNull($pivotTable . '.outline')
                        ->whereNotNull($pivotTable . '.is_workhour')
                        ->where(function ($q) use ($pivotTable) {
                            $q->whereNull($pivotTable . '.image_verified')
                                ->orWhereNull($pivotTable . '.content_verified')
                                ->orWhereNull($pivotTable . '.letter_verified');
                        });
                })
                ->select('staff.id', 'staff.name', 'chairs.name as chair_name')
                ->selectRaw('COUNT(' . $pivotTable . '.duty_id) as duties_count')
                ->groupBy('staff.id', 'staff.name', 'chairs.name')
                ->orderBy('staff.id');
                
                return $query;
            })
            ->columns([
                TextColumn::make('no')
                    ->label('#')
                    ->rowIndex()
                    ->sortable(false)
                    ->toggleable(false)
                    ->width('80px'),
                TextColumn::make('name')->label('Nama Pegawai')->sortable()->searchable(),
                TextColumn::make('chair_name')->label('Jabatan'),
                TextColumn::make('duties_count')
                    ->label('Belum Direspon')
                    ->getStateUsing(function ($record) {
                        return $record->duties_count ?: '--';
                    })
                    ->badge(function ($record) {
                        return $record->duties_count ?: false;
                    })
                    ->alignCenter(),
            ])
            ->filters([
            ])
            ->recordActions([
                Action::make('lihatTugas')
                    ->label('Lihat Laporan') 
                    ->url(fn ($record) => DutyResource::getUrl('approve', ['record' => $record->id])),
            ])
            ->recordAction('lihatTugas')
            ->recordUrl(null)
            ->defaultSort(null);
    }
    
}
