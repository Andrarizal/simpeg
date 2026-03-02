<?php

namespace App\Filament\Resources\Duties;

use App\Filament\Resources\Duties\Pages\CreateDuty;
use App\Filament\Resources\Duties\Pages\EditDuty;
use App\Filament\Resources\Duties\Pages\ListDuties;
use App\Filament\Resources\Duties\Schemas\DutyForm;
use App\Filament\Resources\Duties\Tables\DutiesTable;
use App\Models\Duty;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DutyResource extends Resource
{
    protected static ?string $model = Duty::class;

    protected static ?string $modelLabel = 'Penugasan';       
    protected static ?string $pluralModelLabel = 'Penugasan'; 
    protected static ?string $navigationLabel = 'Penugasan';
    protected static ?int $navigationSort = 2;
    protected static UnitEnum|string|null $navigationGroup = 'Surat';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'Duty';

    public static function form(Schema $schema): Schema
    {
        return DutyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DutiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDuties::route('/'),
            'create' => CreateDuty::route('/create'),
            'edit' => EditDuty::route('/{record}/edit'),
        ];
    }
}
