<?php

namespace App\Filament\Resources\Letters;

use App\Filament\Resources\Letters\Pages\CreateLetter;
use App\Filament\Resources\Letters\Pages\EditLetter;
use App\Filament\Resources\Letters\Pages\ListLetters;
use App\Filament\Resources\Letters\Schemas\LetterForm;
use App\Filament\Resources\Letters\Tables\LettersTable;
use App\Models\Letter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class LetterResource extends Resource
{
    protected static ?string $model = Letter::class;

    protected static ?int $navigationSort = 1;
    protected static UnitEnum|string|null $navigationGroup = 'Perusahaan';

    public static function isSubordinate(): bool
    {
        $user = Auth::user();
        if (!$user || !$user->staff || !$user->staff->chair) {
            return false;
        }

        return str_contains($user->staff->chair->name, 'Sekretariat');
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return self::isSubordinate() 
            ? Heroicon::EnvelopeOpen 
            : Heroicon::Envelope;
    }

    public static function getModelLabel(): string
    {
        return self::isSubordinate() 
            ? 'Surat Keluar' 
            : 'Surat Masuk';
    }

    public static function getPluralModelLabel(): string
    {
        return self::isSubordinate() 
            ? 'Kotak Keluar' 
            : 'Kotak Masuk';
    }

    public static function getNavigationLabel(): string
    {
        return self::isSubordinate() 
            ? 'Surat Keluar' 
            : 'Surat Masuk';
    }

    protected static ?string $recordTitleAttribute = 'Letter';

    public static function form(Schema $schema): Schema
    {
        return LetterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LettersTable::configure($table);
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
            'index' => ListLetters::route('/'),
            'create' => CreateLetter::route('/create'),
            'edit' => EditLetter::route('/{record}/edit'),
        ];
    }
}
