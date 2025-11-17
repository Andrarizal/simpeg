<?php

namespace App\Filament\Resources\Presences;

use App\Filament\Resources\Presences\Pages\ManagePresences;
use App\Models\Presence;
use BackedEnum;
use Carbon\Carbon;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
            ->query(function (): Builder {
                return Presence::where('staff_id', Auth::user()->staff_id);
            })
            ->columns([
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
                SelectFilter::make('month_year')
                    ->label('Bulan')
                    ->options(
                        collect(range(0, 11))
                            ->mapWithKeys(fn ($i) => [
                                now()->subMonths($i)->format('Y-m') =>
                                    now()->subMonths($i)->translatedFormat('F Y'),
                            ])
                    )
                    ->default(now()->format('Y-m'))
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return;

                        $date = Carbon::createFromFormat('Y-m', $data['value']);

                        $query->whereMonth('presence_date', $date->month)
                            ->whereYear('presence_date', $date->year);
                    })
                    ->indicateUsing(function (array $data) {
                        if (empty($data['value'])) return [];

                        return [
                            'Bulan: ' . Carbon::parse($data['value'])->translatedFormat('F Y'),
                        ];
                    })
                    ->selectablePlaceholder(false)
                    ->native(false)
            ])
            ->hiddenFilterIndicators()
            ->recordActions([
                
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
