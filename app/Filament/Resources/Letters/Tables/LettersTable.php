<?php

namespace App\Filament\Resources\Letters\Tables;

use App\Filament\Pages\Signature;
use App\Models\Letter;
use App\Models\LetterReceiver;
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

class LettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                if (str_contains(Auth::user()->staff->chair->name, 'Sekretariat')){
                    return $query->latest();
                } else {
                    $letters = LetterReceiver::where('staff_id', Auth::user()->staff_id)->pluck('letter_id')->toArray();
                    foreach ($letters as $letterId) {
                        $query->orWhere('id', $letterId);
                    }
                    return $query->latest();
                }
            })
            ->columns([
                TextColumn::make('classification')
                    ->label('Jenis'),
                TextColumn::make('sender')
                    ->label('Pengirim')
                    ->default('Sekretariat')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Acara/Perihal')
                    ->searchable()
                    ->wrap()
                    ->formatStateUsing(fn (string $state): string => substr(nl2br(e($state)), 0, 100) . (strlen(nl2br(e($state))) > 100 ? '...' : ''))
                    ->html()
                    ->extraAttributes([
                        'class' => 'min-w-xs', 
                    ]),
                TextColumn::make('created_at')
                    ->label('Tanggal Distribusi')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('agenda_date')
                    ->label('Tanggal Agenda')
                    ->state(fn ($record) => $record->end_date ? Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' - ' . Carbon::parse($record->end_date)->translatedFormat('d F Y') : Carbon::parse($record->start_date)->translatedFormat('d F Y'))
                    ->wrap()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('month_year')
                    ->label('Bulan')
                    ->options(function () {
                        return Letter::query()
                            ->select('letter_date')
                            ->whereNotNull('letter_date')
                            ->orderBy('letter_date', 'desc') 
                            ->get()
                            ->map(function ($item) {
                                return Carbon::parse($item->letter_date)->format('Y-m');
                            })
                            ->unique() 
                            ->mapWithKeys(function ($dateString) {
                                return [
                                    $dateString => Carbon::createFromFormat('!Y-m', $dateString)->translatedFormat('F Y')
                                ];
                            })
                            ->toArray();
                    })
                    ->default(function () {
                        $latestLetter = Letter::whereNotNull('letter_date')
                            ->orderBy('letter_date', 'desc')
                            ->first();
                        return $latestLetter 
                            ? Carbon::parse($latestLetter->letter_date)->format('Y-m') 
                            : now()->format('Y-m');
                    })
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return;

                        $date = Carbon::createFromFormat('Y-m', $data['value']);

                        $query->whereMonth('letter_date', $date->month)
                            ->whereYear('letter_date', $date->year);
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
                Action::make('previewDisposition')
                    ->label('Lihat')
                    ->visible(fn ($record) => $record->classification === 'Disposisi')
                    ->icon('heroicon-o-eye') 
                    ->color('info')
                    ->modalHeading(false)
                    ->modalWidth('5xl') 
                    ->modalContent(function ($record, $livewire) {
                        $knownData = [
                            'known_by' => $record['known_by'],
                            'known_at' => $record['created_at']
                        ];
                        $known_url = Signature::getUrl($knownData);
                        $known = base64_encode(QrCode::format('svg')->size(48)->generate($known_url));

                        $html = view('exports.disposition', ['record' => $record, 'known' => $known])->render();

                        $mpdf = new Mpdf([
                            'mode' => 'utf-8', 
                            'format' => 'A4',
                            'margin_top' => 10,
                            'margin_left' => 15,
                            'margin_right' => 15,
                            'margin_bottom' => 10,
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
                Action::make('previewInvitation')
                    ->label('Lihat')
                    ->visible(fn ($record) => $record->classification === 'Undangan')
                    ->icon('heroicon-o-eye') 
                    ->color('info')
                    ->modalHeading(false)
                    ->modalWidth('5xl') 
                    ->modalContent(function ($record, $livewire) {
                        $knownData = [
                            'known_by' => $record['known_by'],
                            'known_at' => $record['created_at']
                        ];
                        $known_url = Signature::getUrl($knownData);
                        $known = base64_encode(QrCode::format('svg')->size(100)->generate($known_url));

                        $html = view('exports.invitation', ['record' => $record, 'known' => $known])->render();

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
                Action::make('outline')
                    ->label('Notulensi')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->url(fn ($record): string => route('filament.admin.resources.letters.outline', ['record' => $record]))
                    ->visible(fn ($record) => $record->receiver()->where('staff_id', Auth::user()->staff_id)->exists() && $record->classification === 'Undangan'),
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
