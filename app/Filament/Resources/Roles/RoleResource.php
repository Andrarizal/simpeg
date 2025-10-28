<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\ManageRoles;
use App\Models\Role;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $modelLabel = 'Role';        // singular
    protected static ?string $pluralModelLabel = 'List Role'; // plural/menu
    protected static ?string $navigationLabel = 'List Role';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'Role';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Role')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Role')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Role')
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
            'index' => ManageRoles::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->role_id === 1;
    }
}
