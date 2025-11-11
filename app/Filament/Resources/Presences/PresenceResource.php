<?php

namespace App\Filament\Resources\Presences;

use App\Filament\Resources\Presences\Pages\ManagePresences;
use App\Models\Presence;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PresenceResource extends Resource
{
    protected static ?string $model = Presence::class;

    protected static ?string $modelLabel = 'Presensi';       
    protected static ?string $pluralModelLabel = 'Presensi'; 
    protected static ?string $navigationLabel = 'Presensi';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::FingerPrint;

    protected static ?string $recordTitleAttribute = 'Presence';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('staff_id')->default(Auth::user()->staff_id),
                Hidden::make('presence_date')->default(now()->toDateString()),
                Hidden::make('check_in')->default(now()),
                Hidden::make('ssid'),
                Hidden::make('ip'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Presence')
            ->columns([
                TextColumn::make('staff.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('presence_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('check_in')
                    ->label('Masuk')
                    ->time()
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label('Pulang')
                    ->time()
                    ->sortable(),
                TextColumn::make('ssid')
                    ->label('SSID')
                    ->searchable(),
                TextColumn::make('ip')
                    ->label('IP Address')
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
                Action::make('check_out')
                    ->label('Check Out')
                    ->color('warning')
                    ->icon('heroicon-o-logout')
                    ->requiresConfirmation()
                    ->action(function () {
                        $attendance = Presence::where('staff_id', Auth::user()->staff_id)
                            ->whereDate('date', now())
                            ->first();
                    }),
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
            'index' => ManagePresences::route('/'),
        ];
    }
}
