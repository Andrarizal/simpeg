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
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $modelLabel = 'Unit Kerja';        // singular
    protected static ?string $pluralModelLabel = 'Unit Kerja'; // plural/menu
    protected static ?string $navigationLabel = 'Unit Kerja';
    protected static ?int $navigationSort = 3;
    protected static UnitEnum|string|null $navigationGroup = 'Kepegawaian';

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
            ->recordTitleAttribute('Unit')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Unit')
                    ->searchable(),
                TextColumn::make('leader.name')
                    ->label('Kepala Unit')
                    ->sortable(),
                TextColumn::make('work_system')
                    ->label('Sistem Kerja')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('manage_shifts')
                    ->label('Jadwal')
                    ->icon('heroicon-m-clock')
                    ->color('info')
                    ->url(fn (Unit $record): string => UnitResource::getUrl('shifts', ['record' => $record])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
        return Auth::user()?->role_id === 1;
    }
}
