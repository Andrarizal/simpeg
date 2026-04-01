<?php

namespace App\Filament\Resources\ContractEvaluations;

use App\Filament\Pages\Signature;
use App\Filament\Resources\ContractEvaluations\Pages\ManageContractEvaluations;
use App\Models\PerformanceAppraisal;
use App\Models\PerformancePeriod;
use App\Models\Staff;
use App\Models\StaffContractEvaluation;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use UnitEnum;

class ContractEvaluationResource extends Resource
{
    protected static ?string $model = StaffContractEvaluation::class;

    protected static ?string $modelLabel = 'Evaluasi Kontrak';       
    protected static ?string $pluralModelLabel = 'Evaluasi Kontrak'; 
    protected static ?string $navigationLabel = 'Evaluasi Kontrak';
    protected static ?int $navigationSort = 5;
    protected static UnitEnum|string|null $navigationGroup = 'Kepegawaian';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Scale;

    protected static ?string $recordTitleAttribute = 'Evaluasi Kontrak';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('note')
                    ->label('Catatan')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->rows(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ContractEvaluation')
            ->modifyQueryUsing(function ($query) {
                if (Auth::user()->role_id != 1 && !str_contains(Auth::user()->staff->chair->name, 'Tata Usaha')) {
                    $query->whereHas('contract', function ($q) {
                        $q->where('staff_id', Auth::user()->staff->id);
                    });
                }
                $query->latest();
            })
            ->columns([
                TextColumn::make('contract.staff.name')
                    ->label('Nama Pegawai')
                    ->visible(Auth::user()->role_id == 1 || str_contains(Auth::user()->staff->chair->name, 'Tata Usaha'))
                    ->searchable(),
                TextColumn::make('contract_duration')
                    ->label('Durasi Kontrak')
                    ->getStateUsing(function (StaffContractEvaluation $record) {
                        $startDate = $record->contract->start_date;
                        $endDate = $record->contract->end_date;

                        if ($startDate && $endDate) {
                            return date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate));
                        }

                        return '---';
                    })
                    ->alignCenter(),
                TextColumn::make('firstScore.score')
                    ->label('Nilai Pertama')
                    ->placeholder('---')
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 85 => 'info', 
                        $state >= 70 => 'success', 
                        $state >= 50 => 'warning', 
                        $state > 0   => 'danger',  
                        default      => 'gray',
                    }),
                TextColumn::make('secondScore.score')
                    ->label('Nilai Kedua')
                    ->placeholder('---')
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 85 => 'info', 
                        $state >= 70 => 'success', 
                        $state >= 50 => 'warning', 
                        $state > 0   => 'danger',  
                        default      => 'gray',
                    }),
                TextColumn::make('final_score')
                    ->label('Nilai Akhir')
                    ->placeholder('---')
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 85 => 'info', 
                        $state >= 70 => 'success', 
                        $state >= 50 => 'warning', 
                        $state > 0   => 'danger',  
                        default      => 'gray',
                    }),
                TextColumn::make('conclusion')
                    ->label('Kesimpulan')
                    ->searchable()
                    ->placeholder('---')
                    ->badge()
                    ->alignCenter()
                    ->color(function ($state) {
                        if ($state === 'Lulus') {
                            return 'success';
                        } else if ($state === 'Tidak Lulus') {
                            return 'danger';
                        } else {
                            return 'gray';
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('evaluation')
                    ->label('Evaluasi')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn () => Auth::user()->role_id == 1)
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Evaluasi')
                    ->modalDescription('Apakah nilai untuk masing-masing semester telah diinputkan?')
                    ->modalSubmitActionLabel('Ya, Sudah')
                    ->action(function ($record) {
                        $periods = PerformancePeriod::where(function ($query) use ($record) {
                            $query->whereBetween('start_date', [$record->contract->start_date, $record->contract->end_date])
                                ->orWhereBetween('end_date', [$record->contract->start_date, $record->contract->end_date])
                                ->orWhere(function ($q) use ($record) {
                                    $q->where('start_date', '<=', $record->contract->start_date)
                                        ->where('end_date', '>=', $record->contract->end_date);
                                });
                        })
                        ->orderBy('start_date', 'asc')
                        ->limit(2)
                        ->get();

                        if ($periods->count() < 2) {
                            Notification::make()
                                ->title('Periode penilaian yang beririsan kurang dari 2 semester.')
                                ->danger()->send();
                            return;
                        }

                        $appraisal1 = PerformanceAppraisal::whereHas('target', function ($q) use ($record, $periods) {
                            $q->where('staff_id', $record->contract->staff_id)
                            ->where('period_id', $periods[0]->id);
                        })->first();

                        $appraisal2 = PerformanceAppraisal::whereHas('target', function ($q) use ($record, $periods) {
                            $q->where('staff_id', $record->contract->staff_id)
                            ->where('period_id', $periods[1]->id);
                        })->first();

                        if (!$appraisal1 || !$appraisal2) {
                            Notification::make()
                                ->title('Nilai appraisal belum lengkap untuk 2 semester tersebut.')
                                ->danger()->send();
                            return;
                        }

                        $finalScore = ($appraisal1->score + $appraisal2->score) / 2;
                        $kkm = setting('minimum_passing_grade'); 
                        $conclusion = $finalScore >= $kkm ? 'Lulus' : 'Tidak Lulus';

                        StaffContractEvaluation::updateOrCreate(
                            ['contract_id' => $record->id],
                            [
                                'first_score_id' => $appraisal1->id,
                                'second_score_id' => $appraisal2->id,
                                'final_score' => $finalScore,
                                'conclusion' => $conclusion,
                            ]
                        );

                        Notification::make()
                            ->title('Kontrak Berhasil Dievaluasi!')
                            ->success()->send();

                        Notification::make()
                            ->title('Kontrak Anda Telah Dievaluasi')
                            ->body('Kontrak Anda untuk ' . Carbon::parse($record->contract->start_date)->translatedFormat('d F Y') . ' - ' . Carbon::parse($record->contract->end_date)->translatedFormat('d F Y') . ' telah dievaluasi oleh SDM')
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(ContractEvaluationResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->contract->staff->user);
                    }),
                EditAction::make()
                    ->label('Beri Catatan')
                    ->visible(str_contains(Auth::user()->staff->chair->name, 'Tata Usaha'))
                    ->color('info')
                    ->modalWidth('md')
                    ->modalFooterActionsAlignment(Alignment::End),
                ActionGroup::make([
                    Action::make('exportPdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('warning')
                        ->visible()
                        ->modalHeading('Preview Evaluasi Kontrak')
                        ->modalWidth('5xl')
                        ->modalContent(function ($record, $livewire) {
                            $administrator = Staff::whereHas('chair', function($q) {
                                $q->where('name', 'LIKE', '%Tata Usaha%');
                            })->first();
                            
                            $knownData = [
                                'known_by' => $administrator->id,
                                'known_at' => $record['created_at']
                            ];
                            $known_url = Signature::getUrl($knownData);
                            $known = base64_encode(QrCode::format('svg')->size(100)->generate($known_url));
                            
                            $html = view('exports.contract', [
                                'contract' => $record,
                                'known' => $known,
                                'administrator' => $administrator,
                            ])->render();

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
                            
                            $token = Str::uuid()->toString();
                            $pdfPath = storage_path("app/private/livewire-tmp/$token.pdf");
                            file_put_contents($pdfPath, $mpdf->Output('', 'S'));
                            $livewire->pdfToken = $token;

                            return view('filament.components.preview-pdf', ['token' => $token]);
                        })
                        ->modalHeading(false)
                        ->modalCancelAction(false)
                        ->modalSubmitAction(false)
                        ->modalCloseButton(false)
                        ->closeModalByClickingAway(false)
                        ->closeModalByEscaping(false)
                        ->extraAttributes([
                            'x-on:click.capture' => 'close()'
                        ]),
                    Action::make('exportWord')
                        ->label('Export Word')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->visible(fn() => Auth::user()->role_id == 1 || str_contains(Auth::user()->staff->chair->name, 'Tata Usaha'))
                        ->action(function ($record, $livewire) {
                            $administrator = Staff::whereHas('chair', function($q) {
                                $q->where('name', 'LIKE', '%Tata Usaha%');
                            })->first();
                            
                            $knownData = [
                                'known_by' => $administrator->id,
                                'known_at' => $record['created_at']
                            ];
                            $known_url = Signature::getUrl($knownData);
                            $known = base64_encode(QrCode::format('svg')->size(100)->generate($known_url));

                            $name = $record->contract->staff->name ?? 'Pegawai';
                            $html = view('exports.presences', [
                                'contract' => $record,
                                'known' => $known,
                                'administrator' => $administrator,
                            ])->render();

                            $fileName = 'Evaluasi Kontrak_' . $name . '.doc';

                            return response()->streamDownload(function () use ($html) {
                                echo '<meta charset="UTF-8">';
                                echo $html;
                            }, $fileName, [
                                'Content-Type' => 'application/vnd.ms-word',
                                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                            ]);
                        }),
                    ])
                    ->label('Export Data')
                    ->link() 
                    ->icon('heroicon-m-arrow-top-right-on-square') 
                    ->color('success'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContractEvaluations::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->role_id == 1 || Auth::user()->staff->staffStatus->name == "Kontrak" || str_contains(Auth::user()->staff->chair->name, 'Tata Usaha');
    }
}
