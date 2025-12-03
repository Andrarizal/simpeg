<?php

namespace App\Filament\Resources\Overtimes\Tables;

use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Models\Overtime;
use App\Models\Staff;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $staff = Auth::user()->staff;
                $query = Staff::query();

                if (Auth::user()->staff->chair->level > 2){
                    $query->leftJoin('chairs', 'staff.chair_id', '=', 'chairs.id')
                        ->where('chairs.level', '=', 4);
                        if (Auth::user()->role_id != 1){
                            $query->leftJoin('units', 'staff.unit_id', '=', 'units.id')
                                ->where($staff->chair->level === 4 ? 'units.leader_id' : 'chairs.head_id', $staff->chair_id)
                                ->where('staff.id', '!=', $staff->id);
                        }
                        $query->leftJoin('overtimes', function ($join) {
                            $user = Auth::user();
                            $chairLevel = $user->staff->chair->level;

                            if ($user->role_id === 1){
                                $join->on('overtimes.staff_id', '=', 'staff.id')
                                    ->whereNull('overtimes.is_verified');
                            } else {
                                $join->on('overtimes.staff_id', '=', 'staff.id')
                                    ->where(function ($q) use ($chairLevel) {
                                        $q->whereNull('overtimes.is_known');
                                        if ($chairLevel === 3){
                                            $q->orWhere('overtimes.is_known', 1);
                                        }
                                    });
                            }
                        })
                        ->select('staff.id', 'staff.name', 'chairs.name as chair_name')
                        ->selectRaw('COUNT(overtimes.id) as overtimes_count')
                        ->groupBy('staff.id', 'staff.name', 'chairs.name')
                        ->orderBy('staff.id');
                } else {
                    $chair = $staff->chair;
                    $chairIds = $chair->allSubordinateIds();

                    $query->whereIn('chairs.id', $chairIds)
                        ->where('chairs.level', '=', 4)
                        ->select([
                            'staff.id',
                            'staff.name',
                            'chairs.name as chair_name',
                            DB::raw('COUNT(overtimes.id) as overtimes_count'),
                        ])
                        ->leftJoin('chairs', 'chairs.id', '=', 'staff.chair_id')
                        ->leftJoin('overtimes', function ($join) {
                            $join->on('overtimes.staff_id', '=', 'staff.id')
                                ->where(function ($q) {
                                    $q->whereNull('overtimes.is_known')
                                    ->orWhereNull('overtimes.is_verified');
                                });
                        })
                        ->groupBy('staff.id', 'staff.name', 'chairs.name');
                }
                
                // dd($query->get());
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
                TextColumn::make('overtimes_count')
                    ->label('Belum ' . (Auth::user()->staff->chair->level < 3 ? 'Selesai ' : '') . 'Direspon')
                    ->getStateUsing(function ($record) {
                        return $record->overtimes_count ?: '--';
                    })
                    ->badge(function ($record) {
                        return $record->overtimes_count ?: false;
                    })
                    ->alignCenter(),
            ])
            ->filters([
            ])
            ->recordActions([
                Action::make('lihatLembur')
                    ->label('Lihat Lembur')
                    ->url(fn ($record) => OvertimeResource::getUrl('approve', ['record' => $record->id])),
            ])
            ->recordAction('lihatLembur')
            ->recordUrl(null)
            ->defaultSort(null);
    }
    
}
