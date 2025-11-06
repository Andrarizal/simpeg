<?php

namespace App\Filament\Resources\StaffAdministrations;

use App\Filament\Resources\StaffAdministrations\Pages\CreateStaffAdministration;
use App\Filament\Resources\StaffAdministrations\Pages\EditStaffAdministration;
use App\Filament\Resources\StaffAdministrations\Pages\ListStaffAdministrations;
use App\Filament\Resources\StaffAdministrations\Pages\ViewStaffAdministration;
use App\Filament\Resources\StaffAdministrations\Schemas\StaffAdministrationForm;
use App\Filament\Resources\StaffAdministrations\Schemas\StaffAdministrationInfolist;
use App\Filament\Resources\StaffAdministrations\Tables\StaffAdministrationsTable;
use App\Models\Staff;
use App\Models\StaffAdministration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StaffAdministrationResource extends Resource
{
    protected static ?string $model = StaffAdministration::class;

    protected static ?string $modelLabel = 'Administrasi';       
    protected static ?string $pluralModelLabel = 'Administrasi Pegawai'; 
    protected static ?string $navigationLabel = 'Administrasi';
    protected static ?int $navigationSort = 6;
    protected static UnitEnum|string|null $navigationGroup = 'Kepegawaian';
    
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Wallet;

    protected static ?string $recordTitleAttribute = 'Administration';

    public static function form(Schema $schema): Schema
    {
        return StaffAdministrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StaffAdministrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffAdministrationsTable::configure($table);
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
            'index' => ListStaffAdministrations::route('/'),
            'view' => ViewStaffAdministration::route('/{record}'),
            'edit' => EditStaffAdministration::route('/{record}/edit'),
        ];
    }
}
