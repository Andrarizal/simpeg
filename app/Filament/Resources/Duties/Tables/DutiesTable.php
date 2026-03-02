<?php

namespace App\Filament\Resources\Duties\Tables;

use App\Filament\Pages\Signature;
use App\Models\Duty;
use App\Models\Staff;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DutiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('duty_date')
                    ->label('Tanggal Penugasan')
                    ->date('l, d F Y')
                    ->sortable(),
                TextColumn::make('time')
                    ->label('Waktu Penugasan')
                    ->state(function ($record) {
                        $start = Carbon::parse($record->start_time)->translatedFormat('H:i');
                        $end = $record->end_time ? Carbon::parse($record->end_time)->translatedFormat('H:i') : 'selesai';
                        return "$start - $end";
                    }),
                TextColumn::make('location')
                    ->label('Tempat'),
                TextColumn::make('duty')
                    ->label('Acara')
                    ->searchable()
                    ->wrap()
                    ->formatStateUsing(fn (string $state): string => substr(nl2br(e($state)), 0, 100) . (strlen(nl2br(e($state))) > 100 ? '...' : ''))
                    ->html()
                    ->extraAttributes([
                        'class' => 'min-w-xs', 
                    ]),
                TextColumn::make('transportation')
                    ->label('Transportasi'),
            ])
            ->filters([
                SelectFilter::make('month_year')
                    ->label('Bulan')
                    ->options(function () {
                        return Duty::query()
                            ->select('duty_date')
                            ->whereNotNull('duty_date')
                            ->orderBy('duty_date', 'desc') 
                            ->get()
                            ->map(function ($item) {
                                return Carbon::parse($item->duty_date)->format('Y-m');
                            })
                            ->unique() 
                            ->mapWithKeys(function ($dateString) {
                                return [
                                    $dateString => Carbon::createFromFormat('Y-m', $dateString)->translatedFormat('F Y')
                                ];
                            })
                            ->toArray();
                    })
                    ->default(now()->format('Y-m'))
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return;

                        $date = Carbon::createFromFormat('Y-m', $data['value']);

                        $query->whereMonth('duty_date', $date->month)
                            ->whereYear('duty_date', $date->year);
                    })
                    ->indicateUsing(function (array $data) {
                        if (empty($data['value'])) return [];

                        return [
                            Indicator::make('Bulan: ' . Carbon::parse($data['value'])->translatedFormat('F Y'))
                            ->removable(false),
                        ];
                    })
                    ->selectablePlaceholder(false)
                    ->native(false)
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye') 
                    ->color('info')
                    ->modalHeading(false)
                    ->modalWidth('5xl') 
                    ->modalContent(function ($record, $livewire) {
                        $knownData = [
                            'known_by' => Staff::where('chair_id', 1)->first()->id,
                            'known_at' => $record['created_at']
                        ];
                        $known_url = Signature::getUrl($knownData);
                        $known = base64_encode(QrCode::format('svg')->size(100)->generate($known_url));

                        $html = view('exports.duty', ['record' => $record, 'known' => $known])->render();

                        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                        $fontDirs = $defaultConfig['fontDir'];

                        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                        $fontData = $defaultFontConfig['fontdata'];

                        $mpdf = new Mpdf([
                            'mode' => 'utf-8', 
                            'format' => [215.9, 342.9],
                            'fontDir' => array_merge($fontDirs, [
                                public_path('fonts'), 
                            ]),
                            'fontdata' => $fontData + [
                                'tnr' => [
                                    'R' => 'times.ttf',    
                                    'B' => 'timesbd.ttf',  
                                    'I' => 'timesi.ttf',   
                                    'BI' => 'timesbi.ttf',  
                                ]
                            ],
                            'default_font' => 'tnr',
                            'margin_top' => 15,
                            'margin_left' => 20,
                            'margin_right' => 20,
                            'margin_bottom' => 15,
                        ]);

                        $mpdf->WriteHTML($html);

                        $pathFileAsli = storage_path('app/public/' . $record->file_path);

                        if (file_exists($pathFileAsli)) {
                            try {
                                $pageCount = $mpdf->SetSourceFile($pathFileAsli);

                                for ($i = 1; $i <= $pageCount; $i++) {
                                    $mpdf->AddPage(); 
                                    $tplId = $mpdf->ImportPage($i);
                                    $mpdf->UseTemplate($tplId, ['adjustPageSize' => true]); 
                                }
                            } catch (\Exception $e) {
                            }
                        }

                        $token = Str::uuid()->toString();
                        
                        $tempPath = storage_path("app/private/livewire-tmp/$token.pdf");
                        
                        file_put_contents($tempPath, $mpdf->Output('', 'S'));

                        if (property_exists($livewire, 'pdfToken')) {
                            $livewire->pdfToken = $token;
                        }

                        return view('filament.components.preview-pdf', [
                            'token' => $token,
                        ]);
                    })
                    ->modalHeading(false)
                    ->modalCancelAction(false)
                    ->modalSubmitAction(false)
                    ->modalCloseButton(false)
                    ->closeModalByClickingAway(false)
                    ->closeModalByEscaping(false),
                EditAction::make()
                    ->visible(fn () => str_contains(Auth::user()->staff->chair->name, 'Sekretariat')),
                DeleteAction::make()
                    ->visible(fn () => str_contains(Auth::user()->staff->chair->name, 'Sekretariat'))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
