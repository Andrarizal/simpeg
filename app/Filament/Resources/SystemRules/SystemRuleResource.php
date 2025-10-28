<?php

namespace App\Filament\Resources\SystemRules;

use App\Filament\Resources\SystemRules\Pages\CreateSystemRule;
use App\Filament\Resources\SystemRules\Pages\EditSystemRule;
use App\Filament\Resources\SystemRules\Pages\ListSystemRules;
use App\Filament\Resources\SystemRules\Schemas\SystemRuleForm;
use App\Filament\Resources\SystemRules\Tables\SystemRulesTable;
use App\Models\SystemRule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SystemRuleResource extends Resource
{
    protected static ?string $model = SystemRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;

    protected static ?string $recordTitleAttribute = 'SystemRule';

    protected static ?string $modelLabel = 'Aturan Sistem';        // singular
    protected static ?string $pluralModelLabel = 'Aturan Sistem'; // plural/menu
    protected static ?string $navigationLabel = 'Aturan Sistem';

    public static function form(Schema $schema): Schema
    {
        return SystemRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemRulesTable::configure($table);
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
            'index' => ListSystemRules::route('/'),
            'create' => CreateSystemRule::route('/create'),
            'edit' => EditSystemRule::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->role_id === 1;
    }
}
