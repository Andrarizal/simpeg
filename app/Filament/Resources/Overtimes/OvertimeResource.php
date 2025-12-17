<?php

namespace App\Filament\Resources\Overtimes;

use App\Filament\Resources\Overtimes\Pages\ApproveOvertime;
use App\Filament\Resources\Overtimes\Pages\CreateOvertime;
use App\Filament\Resources\Overtimes\Pages\EditOvertime;
use App\Filament\Resources\Overtimes\Pages\ListOvertimes;
use App\Filament\Resources\Overtimes\Pages\ViewOvertime;
use App\Filament\Resources\Overtimes\Schemas\OvertimeForm;
use App\Filament\Resources\Overtimes\Schemas\OvertimeInfolist;
use App\Filament\Resources\Overtimes\Tables\OvertimesTable;
use App\Models\Overtime;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OvertimeResource extends Resource
{
    protected static ?string $model = Overtime::class;

    protected static ?string $modelLabel = 'Lembur';       
    protected static ?string $pluralModelLabel = 'Daftar Lembur'; 
    protected static ?string $navigationLabel = 'Lembur';
    protected static ?int $navigationSort = 7;
    protected static UnitEnum|string|null $navigationGroup = 'Kepegawaian';
    
    protected static string|BackedEnum|null $navigationIcon = Heroicon::SquaresPlus;

    protected static ?string $recordTitleAttribute = 'Overtime';

    public static function form(Schema $schema): Schema
    {
        return OvertimeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OvertimeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OvertimesTable::configure($table);
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
            'index' => ListOvertimes::route('/'),
            'create' => CreateOvertime::route('/create'),
            'view' => ViewOvertime::route('/{record}'),
            'edit' => EditOvertime::route('/{record}/edit'),
            'approve' => ApproveOvertime::route('/{record}/approve')
        ];
    }
}
