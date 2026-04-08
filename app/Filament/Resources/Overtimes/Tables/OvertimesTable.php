<?php

namespace App\Filament\Resources\Overtimes\Tables;

use App\Filament\Pages\Signature;
use App\Models\MonthlyPeriod;
use App\Models\Overtime;
use App\Models\Staff;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OvertimesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                ActionGroup::make([
                    Action::make('exportPdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('warning')
                        ->modalHeading('Preview Lembur')
                        ->modalWidth('5xl')
                        ->modalContent(function ($livewire) {
                            $month = $livewire->tableFilters['period_id']['value'];

                            $period = MonthlyPeriod::find($month);

                            $data = Overtime::query()
                                ->with(['staff.chair', 'staff.unit'])
                                ->where('staff_id', Auth::user()->staff_id)
                                ->where('period_id', $month)
                                ->orderBy('overtime_date')
                                ->get();

                                
                            if ($data->isEmpty()) {
                                return view('filament.components.alert', [
                                    'message' => 'Tidak ada data lembur untuk periode terpilih.',
                                    'color'   => 'warning',
                                ]);
                            }
                                
                            $head = Staff::select('name')->where('chair_id', $data[0]->staff->chair->head_id)->first()?->name;
                                    
                            if (!$head) {
                                return view('filament.components.alert', [
                                    'message' => 'Atasan user belum dipilih! Tidak dapat melanjutkan proses.',
                                    'color'   => 'danger',
                                ]);
                            }
                            
                            $sdm = Staff::select('name')->whereHas('chair', fn ($q) => $q->where('name', 'like', '%SDM%'))->first()?->name;

                            foreach ($data as $i => $p) {
                                if (!$p->is_verified) {
                                    $livewire->verified = false;
                                    break;
                                }
                            }

                            foreach ($data as $i => $p) {
                                if ($p->is_known != 2) {
                                    $livewire->known = false;
                                    break;
                                }
                            }

                            $signData = [
                                'known' => null,
                                'verified' => null,
                            ];

                            if ($livewire->known) {
                                $knownData = [
                                    'known_by' => $data[0]['known_by'],
                                    'known_at' => $data[0]['known_at']
                                ];
                                $known_url = Signature::getUrl($knownData);
                                $signData['known'] = base64_encode(QrCode::format('svg')->size(100)->generate($known_url));
                            } 

                            if ($livewire->verified) {
                                $verifiedData = [
                                    'verified_by' => $data[0]['verified_by'],
                                    'verified_at' => $data[0]['verified_at']
                                ];
                                $verified_url = Signature::getUrl($verifiedData);
                                $signData['verified'] = base64_encode(QrCode::format('svg')->size(100)->generate($verified_url));
                            } 

                            $html = view('exports.overtimes', [
                                'data' => $data,
                                'month' => $period->name,
                                'head' => $head,
                                'sdm' => $sdm,
                                'qrCode' => $signData,
                                'known' => $livewire->known,
                                'verified' => $livewire->verified,
                                'isWord' => false
                            ])->render();

                            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                            $fontDirs = $defaultConfig['fontDir'];

                            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                            $fontData = $defaultFontConfig['fontdata'];

                            $mpdf = new Mpdf([
                                'mode' => 'utf-8', 
                                'orientation' => 'L',
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

                            return view('filament.components.preview-pdf', [
                                'token' => $token,
                            ]);
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
                        ->action(function ($livewire) {
                            $month = $livewire->tableFilters['period_id']['value'] ?? null;

                            if ($month) {
                                $period = MonthlyPeriod::find($month);
                            } else {
                                $period = MonthlyPeriod::whereDate('start_date', '<=', now())
                                    ->whereDate('end_date', '>=', now())
                                    ->first();
                            }

                            $data = Overtime::query()
                                ->with(['staff.chair', 'staff.unit'])
                                ->where('staff_id', Auth::user()->staff_id)
                                ->where('period_id', $month)
                                ->orderBy('overtime_date')
                                ->get();

                                
                            if ($data->isEmpty()) {
                                Notification::make()
                                    ->title('Tidak ada data lembur untuk periode terpilih.')
                                    ->warning()
                                    ->send();
                                return;
                            }
                                
                            $head = Staff::select('name')->where('chair_id', $data[0]->staff->chair->head_id)->first()?->name;
                                    
                            if (!$head) {
                                Notification::make()
                                    ->title('Atasan user belum dipilih! Tidak dapat melanjutkan proses.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            
                            $sdm = Staff::select('name')->whereHas('chair', fn ($q) => $q->where('name', 'like', '%SDM%'))->first()?->name;

                            foreach ($data as $i => $p) {
                                if (!$p->is_verified) {
                                    $livewire->verified = false;
                                    break;
                                }
                            }

                            foreach ($data as $i => $p) {
                                if ($p->is_known != 2) {
                                    $livewire->known = false;
                                    break;
                                }
                            }

                            $signData = [
                                'known' => null,
                                'verified' => null,
                            ];

                            if ($livewire->known) {
                                $knownData = [
                                    'known_by' => $data[0]['known_by'],
                                    'known_at' => $data[0]['known_at']
                                ];
                                $known_url = Signature::getUrl($knownData);
                                $signData['known'] = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&format=png&data={$known_url}";
                            } 

                            if ($livewire->verified) {
                                $verifiedData = [
                                    'verified_by' => $data[0]['verified_by'],
                                    'verified_at' => $data[0]['verified_at']
                                ];
                                $verified_url = Signature::getUrl($verifiedData);
                                $signData['verified'] = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&format=png&data={$verified_url}";
                            } 

                            $name = Auth::user()->staff->name ?? 'Pegawai';
                            $html = view('exports.overtimes', [
                                'data' => $data,
                                'month' => $period->name,
                                'head' => $head,
                                'sdm' => $sdm,
                                'qrCode' => $signData,
                                'known' => $livewire->known,
                                'verified' => $livewire->verified,
                                'isWord' => true
                            ])->render();

                            $fileName = 'Lembur_' . $name . '_' . $period->name . '.doc';

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
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->button() 
                ->visible(fn ($livewire) => $livewire->tableFilters['period_id']['value'])
                ->color('success')
                ->dropdownPlacement('bottom-end'),
            ])
            ->query(function (): Builder {
                $query = Overtime::query();

                $query->where('staff_id', Auth::user()->staff_id)
                    ->orderBy('overtime_date', 'DESC')
                    ->orderBy('start_time', 'DESC');

                return $query;
            })
            ->columns([
                TextColumn::make('overtime_date')
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('command')
                    ->label('Perintah')
                    ->wrap()
                    ->extraAttributes(['class' => 'min-w-xs']),
                TextColumn::make('start_time')
                    ->label('Mulai')
                    ->time('H:i')
                    ->alignCenter(),
                TextColumn::make('end_time')
                    ->label('Selesai')
                    ->placeholder('---')
                    ->alignCenter()
                    ->time(fn ($record) => $record->end_time ? 'H:i' : null),
                TextColumn::make('hours')
                    ->label('Total Jam')
                    ->state(function ($record) {
                        if (! $record || ! $record->end_time) {
                            return '---';
                        }

                        $total = $record->getTotalHours();
                        return $total ? "{$total} jam" : '-';
                    })
                    ->alignCenter(),
                IconColumn::make('is_known')
                    ->label('Mengetahui Atasan')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->is_known ?? 'null')
                    ->icon(fn ($state) => match ($state) {
                        2 => 'heroicon-o-check-circle',
                        1 => 'heroicon-o-check-circle',
                        0 => 'heroicon-o-x-circle',
                        'null' => 'heroicon-o-clock',
                    })
                    ->color(fn ($state) => match ($state) {
                        2 => 'info',
                        1 => 'success',
                        0 => 'danger',
                        'null' => 'gray',
                    })
                    ->tooltip(fn ($state) => match ($state) {
                        2 => 'Diketahui Koordinator',
                        1 => 'Diketahui Kepala Unit',
                        0 => 'Ditolak',
                        'null' => 'Belum direspon',
                    }),
                IconColumn::make('is_verified')
                    ->label('Verifikasi SDM')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->is_verified ?? 'null')
                    ->icon(fn ($state) => match ($state) {
                        1 => 'heroicon-o-check-circle',
                        0 => 'heroicon-o-x-circle',
                        'null' => 'heroicon-o-clock',
                    })
                    ->color(fn ($state) => match ($state) {
                        1 => 'info',
                        0 => 'danger',
                        'null' => 'gray',
                    })
                    ->tooltip(fn ($state) => match ($state) {
                        1 => 'Diverifikasi',
                        0 => 'Ditolak',
                        'null' => 'Belum direspon',
                    }),
            ])
            ->filters([
                SelectFilter::make('period_id')
                    ->label('Periode Lembur')
                    ->options(function () {
                        return MonthlyPeriod::orderBy('start_date', 'desc')
                            ->get()
                            ->mapWithKeys(fn ($period) => [$period->id => "{$period->name}"]);
                    })
                    ->default(function () {
                        $period_now = MonthlyPeriod::where('start_date', '<=', now())
                            ->where('end_date', '>=', now())
                            ->value('id');

                        if (!$period_now) {
                            $period_now = MonthlyPeriod::orderBy('start_date', 'desc')->value('id');
                        }
                        
                        return $period_now;
                    })
                    ->query(function (Builder $query, $data) {
                        $query->where('period_id', $data['value']);
                    })
                    ->indicateUsing(function ($data) {
                        if (! $data['value']) {
                            return null;
                        }
                        
                        $periodName = MonthlyPeriod::find($data['value'])?->name;
                        return [
                            Indicator::make('Periode: ' . $periodName)
                                ->removable(false),
                        ];
                    })
                    ->selectablePlaceholder(false)
                    ->native(false),
            ])
            ->recordActions([
                Action::make('selesai')
                    ->label('Selesai')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn($record) => 
                        is_null($record->end_time) && 
                        (
                            Carbon::parse($record->overtime_date)->isToday() ||
                            Carbon::parse($record->overtime_date)->isYesterday()
                        ))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->end_time = now()->format('H:i');
                        $record->calculateTotalHours();
                        $record->save();

                        Notification::make()
                            ->title('Lembur diselesaikan')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn($record) => is_null($record->is_verified) && is_null($record->is_known)),
                DeleteAction::make()
                    ->visible(fn($record) => is_null($record->is_verified) && is_null($record->is_known) && is_null($record->end_time)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('overtime_date', 'desc');
    }
}
