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
use Filament\Notifications\Notification;
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
                    ->options(function (?Chair $record) {
                        $query = Chair::query();

                        if ($record) {
                            $query->where('id', '!=', $record->id);
                        }

                        return $query->pluck('name', 'id')->toArray();
                    })
                    ->preload()
                    ->inlineLabel()
                    ->nullable()
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
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Chair $record, $action) {
                        if ($record->staff()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal menghapus!')
                                ->body('Jabatan ini masih diemban beberapa pegawai, ubah jabatan pegawai tersebut terlebih dahulu')
                                ->send();
                            
                            $action->halt();
                        }
                    }),
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
