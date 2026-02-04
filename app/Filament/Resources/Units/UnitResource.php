<?php

namespace App\Filament\Resources\Units;

use App\Filament\Resources\Units\Pages\ManageUnits;
use App\Filament\Resources\Units\Pages\ManageUnitSchedules;
use App\Models\Unit;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $modelLabel = 'Unit Kerja';
    protected static ?string $pluralModelLabel = 'Unit Kerja';
    protected static ?string $navigationLabel = 'Unit Kerja';
    protected static ?int $navigationSort = 3;
    protected static UnitEnum|string|null $navigationGroup = 'Perusahaan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice;

    protected static ?string $recordTitleAttribute = 'Unit';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->label('Nama Unit')
                    ->required(),
                Select::make('leader_id')
                    ->label('Kepala Unit')
                    ->relationship('leader', 'name')
                    ->native(false),
                Select::make('work_system')
                    ->label('Sistem Kerja')
                    ->options(['Tetap', 'Shift'])
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (Auth::user()->role_id != 1) {
                    $chairId = Auth::user()->staff?->chair_id;

                    if ($chairId) {
                        $query->where(function (Builder $group) use ($chairId) {
                            
                            $group->where(function (Builder $q1) use ($chairId) {
                                $q1->whereNotNull('leader_id')
                                ->whereHas('leader', function (Builder $leaderQ) use ($chairId) {
                                    $leaderQ->where('head_id', $chairId);
                                });
                            })
                            ->orWhere(function (Builder $q2) use ($chairId) {
                                $q2->whereNull('leader_id')
                                ->whereHas('chair', function (Builder $chairQ) use ($chairId) {
                                    $chairQ->where('head_id', $chairId);
                                });
                            });
                            
                        });
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                }
            })
            ->recordTitleAttribute('Unit')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Unit')
                    ->searchable(),
                TextColumn::make('leader.name')
                    ->label('Kepala Unit')
                    ->default('-')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('work_system')
                    ->label('Sistem Kerja')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('manage_shifts')
                    ->label('Jadwal')
                    ->icon('heroicon-m-clock')
                    ->color('info')
                    ->url(fn (Unit $record): string => UnitResource::getUrl('shifts', ['record' => $record])),
                EditAction::make()
                    ->visible(fn () => Auth::user()->role_id == 1),
                DeleteAction::make()
                    ->visible(fn () => Auth::user()->role_id == 1)
                    ->before(function (Unit $record, $action) {
                        if ($record->staff()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal menghapus!')
                                ->body('Unit ini masih memiliki pegawai. Pindahkan pegawai terlebih dahulu.')
                                ->send();
                            
                            $action->halt();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUnits::route('/'),
            'shifts' => ManageUnitSchedules::route('/{record}/shifts'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->staff->chair->level == 3 || Auth::user()->role_id == 1;
    }
}
