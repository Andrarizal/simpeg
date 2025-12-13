<?php

namespace App\Filament\Resources\Chairs;

use App\Filament\Resources\Chairs\Pages\ManageChairs;
use App\Models\Chair;
use BackedEnum;
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

class ChairResource extends Resource
{
    protected static ?string $model = Chair::class;

    protected static ?string $modelLabel = 'Jabatan';      
    protected static ?string $pluralModelLabel = 'Jabatan';
    protected static ?string $navigationLabel = 'Jabatan';
    protected static ?int $navigationSort = 3;
    protected static UnitEnum|string|null $navigationGroup = 'Kepegawaian';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Briefcase;

    protected static ?string $recordTitleAttribute = 'Chair';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Jabatan')
                    ->required()
                    ->inlineLabel()
                    ->columnSpanFull(),
                Select::make('level')
                    ->label('Kedudukan')
                    ->options(['1' => 'Direktur', '2' => 'Kepala Seksi', '3' => 'Koordinator', '4' => 'Karyawan'])
                    ->required()
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->native(false),
                Select::make('head_id')
                    ->label('Bawahan dari')
                    ->searchable()
                    ->options(fn (): array => Chair::query()
                        ->limit(16)
                        ->pluck('name', 'id')
                        ->all())
                    ->preload()
                    ->required()
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Chair')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Jabatan')
                    ->searchable(),
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
            'index' => ManageChairs::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->role_id == 1;
    }
}
