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
use Mpdf\Mpdf;

class PresenceResource extends Resource
{
    protected static ?string $model = Presence::class;

    protected static ?string $modelLabel = 'Presensi';       
    protected static ?string $pluralModelLabel = 'Presensi'; 
    protected static ?string $navigationLabel = 'Presensi';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::FingerPrint;

    protected static ?string $recordTitleAttribute = 'Presence';

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Presence')
            ->query(function (): Builder {
                return Presence::where('staff_id', Auth::user()->staff_id);
            })
            ->headerActions([
                Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->action(function ($livewire) {
                    $month = $livewire->tableFilters['month_year']['value'] ?? now()->format('Y-m');

                    $data = Presence::query()
                        ->with(['staff.chair', 'staff.unit'])
                        ->where('staff_id', Auth::user()->staff_id)
                        ->whereMonth('presence_date', substr($month, 5, 2))
                        ->whereYear('presence_date', substr($month, 0, 4))
                        ->orderBy('presence_date')
                        ->get();

                    $html = view('exports.presences', compact('data', 'month'))->render();

                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'margin_left'   => 25, // 2.5 cm
                        'margin_right'  => 20, // 2 cm
                        'margin_top'    => 25, // 2.5 cm
                        'margin_bottom' => 20, // 2 cm
                    ]);

                    $mpdf->WriteHTML($html);

                    $pdfData = $mpdf->Output('', 'S');

                    return response()->streamDownload(function () use ($pdfData) {
                        echo $pdfData;
                    }, "rekap-absen-$month.pdf");
                }),
            ])
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
                TextColumn::make('method')
                    ->label('Metode Presensi')
                    ->formatStateUsing(fn ($state) => $state === 'network' ? 'Jaringan' : 'Lokasi')
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
