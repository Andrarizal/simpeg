<?php

namespace App\Filament\Resources\Leaves\Tables;

use App\Filament\Pages\Signature;
use App\Filament\Resources\Leaves\LeaveResource;
use App\Models\Leave;
use App\Models\MonthlyPeriod;
use App\Models\Staff;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
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

class ReplacerTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = Leave::query();

                $query->where('replacement_id', Auth::user()->staff_id)
                    ->orderBy('start_date', 'DESC');
                return $query;
            })
            ->columns([
                TextColumn::make('type')
                    ->formatStateUsing(fn ($record) => $record->type . ' ' . $record->subtype)
                    ->label('Jenis'),
                TextColumn::make('staff.name')
                    ->label('Nama')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Dari Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Sampai Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('remaining')
                    ->label('Sisa Cuti')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('replacement.name')
                    ->label('Nama Pengganti')
                    ->default('Tidak Ada')
                    ->alignCenter(),
                IconColumn::make('is_replaced')
                    ->label('Bersedia')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->is_replaced ?? 'null')
                    ->icon(function ($state, $record) {
                        if (! $record->replacement_id) return 'heroicon-o-minus-circle'; 

                        return match ($state) {
                            1 => 'heroicon-o-check-circle',
                            0 => 'heroicon-o-x-circle',
                            default => 'heroicon-o-clock',
                        };
                    })
                    ->color(function ($state, $record) {
                        if (! $record->replacement_id) return 'warning';

                        return match ($state) {
                            1 => 'success',
                            0 => 'danger',
                            default => 'gray',
                        };
                    })
                    ->tooltip(function ($state, $record) {
                        if (! $record->replacement_id) return 'Tidak digantikan';

                        return match ($state) {
                            1 => 'Disetujui',
                            0 => 'Ditolak',
                            default => 'Belum direspon',
                        };
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function ($state, $record) {
                        if ($state == 'Disetujui Kepala Seksi' && optional($record->staff->chair)->level == 3) {
                            return 'Diketahui Kepala Seksi';
                        }
                        return $state;
                    })
                    ->badge()
                    ->alignCenter()
                    ->color(function ($state, $record) {
                        $display = $state;
                        if ($state == 'Disetujui Kepala Seksi' && optional($record->staff->chair)->level == 3) {
                            $display = 'Diketahui Kepala Seksi';
                        }

                        if (str_contains($display, 'Disetujui')) {
                            return 'success';
                        } else if (str_contains($display, 'Diketahui')) {
                            return 'info';
                        } else if (str_contains($display, 'Menunggu')) {
                            return 'warning';
                        } else if (str_contains($display, 'Ditolak')) {
                            return 'danger';
                        } else {
                            return 'gray';
                        }
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
                        1 => 'Disetujui',
                        0 => 'Ditolak',
                        'null' => 'Belum direspon',
                    }),
                TextColumn::make('approver.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('adverb')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('period_id')
                    ->label('Periode Presensi')
                    ->options(function () {
                        return MonthlyPeriod::orderBy('start_date', 'desc')->pluck('name', 'id');
                    })
                    ->query(function (Builder $query, $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        $period = MonthlyPeriod::find($data['value']);

                        if ($period) {
                            $query->whereBetween('start_date', [$period->start_date, $period->end_date]);
                        }
                    })
                    ->indicateUsing(function ($data) {
                        if (! $data['value']) {
                            return null;
                        }
                        
                        $periodName = MonthlyPeriod::find($data['value'])?->name;
                        return [
                            Indicator::make('Periode: ' . $periodName),
                        ];
                    })
                    ->selectablePlaceholder(false)
                    ->native(false),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Bersedia')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => 
                        !$record->is_replaced &&
                        $record->status !== 'Ditolak' &&
                        is_null($record->is_verified))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_replaced' => 1,
                            'replacement_at' => Carbon::now()
                        ]);

                        Notification::make()
                            ->title($record->type . ' Anda bersedia digantikan')
                            ->body('Pengganti Anda telah menyatakan ketersediaannya pada ' . $record->type . ' Anda tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y'))
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(LeaveResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        $head = null;
                        if ($record->staff->chair->level == 4){
                            $head = $record->staff->unit->leader->staff->first() ?? $record->staff->chair->parent->staff->first();
                        } else {
                            $head = $record->staff->chair->parent->staff->first();
                        }

                        Notification::make()
                            ->title("{$record->type} menunggu persetujuan")
                            ->body("{$record->staff->name} telah mengajukan {$record->type} pada tanggal " . Carbon::parse($record->start_date)->translatedFormat('d F Y'))
                            ->warning()
                            ->actions([
                                Action::make('review')
                                    ->label('Tinjau')
                                    ->url(LeaveResource::getUrl('view', ['record' => $record]))
                                    ->markAsRead(),
                            ])
                            ->sendToDatabase($head->user);

                        Notification::make()
                            ->title('Berhasil menyetujui ketersediaan')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn ($record) => 
                        !$record->is_replaced &&
                        $record->status !== 'Ditolak' &&
                        is_null($record->is_verified))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_replaced' => 0,
                            'replacement_at' => Carbon::now()
                        ]);

                        Notification::make()
                            ->title('Pengganti ' . $record->type . ' Anda tidak bersedia')
                            ->body('Pengganti Anda telah menyatakan ketidaksediaannya pada ' . $record->type . ' Anda tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y'))
                            ->warning()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(LeaveResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title('Berhasil menolak ketersediaan')
                            ->success()
                            ->send();
                    }),
                ActionGroup::make([
                    Action::make('exportPdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('warning')
                        ->modalHeading('Preview')
                        ->modalWidth('5xl')
                        ->modalContent(function ($record, $livewire) {
                            $head = Staff::where('chair_id', $record->staff->chair->head_id)->first();
                            $sdm = $record->verifier ?? null;

                            if (!$head) {
                                return view('filament.components.alert', [
                                    'message' => 'Atasan user belum dipilih! Tidak dapat melanjutkan proses.',
                                    'color'   => 'danger',
                                ]);
                            }
                            
                            $approver = '';
                            if ($record->staff->chair->level == 4){
                                $approver = Staff::where('chair_id', $head->chair->head_id)->first()->name;
                            } else {
                                $approver = Staff::where('chair_id', 1)->first()->name;
                            }

                            $signData = [
                                'replace' => null,
                                'known' => null,
                                'approve' => null,
                                'verified' => null,
                            ];

                            if ($record->is_replaced) {
                                $replaceData = [
                                    'replace_by' => $record->replacement_id,
                                    'replace_at' => $record->replacement_at
                                ];
                                $replace_url = Signature::getUrl($replaceData);
                                $signData['replace'] = base64_encode(QrCode::format('svg')->size(100)->generate($replace_url));
                            }

                            if ($record->known_by) {
                                $knownData = [
                                    'known_by' => $record->known_by,
                                    'known_at' => $record->known_at
                                ];
                                $known_url = Signature::getUrl($knownData);
                                $signData['known'] = base64_encode(QrCode::format('svg')->size(100)->generate($known_url));
                            }

                            if (str_contains($record->status, 'Disetujui')) {
                                $approveData = [
                                    'approve_by' => $record->approver_id,
                                    'approve_at' => $record->approve_at
                                ];
                                $approve_url = Signature::getUrl($approveData);
                                $signData['approve'] = base64_encode(QrCode::format('svg')->size(100)->generate($approve_url));
                            }

                            if ($record->is_verified) {
                                $verifiedData = [
                                    'verified_by' => $record->verified_by,
                                    'verified_at' => $record->verified_at
                                ];
                                $verified_url = Signature::getUrl($verifiedData);
                                $signData['verified'] = base64_encode(QrCode::format('svg')->size(100)->generate($verified_url));
                            }

                            $html = view('exports.leaves', [
                                'record' => $record,
                                'head' => $head,
                                'sdm' => $sdm,
                                'approver' => $approver,
                                'qrCode' => $signData,
                                'isWord' => false,
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
                        ->action(function ($record) {
                            $head = Staff::where('chair_id', $record->staff->chair->head_id)->first();
                            $sdm = $record->verifier ?? null;

                            if (!$head) {
                                return Notification::make()
                                    ->title('Atasan user belum dipilih! Tidak dapat melanjutkan proses.')
                                    ->danger()
                                    ->send();
                            }
                            
                            $approver = '';
                            if ($record->staff->chair->level == 4){
                                $approver = Staff::where('chair_id', $head->chair->head_id)->first()->name;
                            } else {
                                $approver = Staff::where('chair_id', 1)->first()->name;
                            }

                            $signData = [
                                'replace' => null,
                                'known' => null,
                                'approve' => null,
                                'verified' => null,
                            ];

                            if ($record->is_replaced) {
                                $replaceData = [
                                    'replace_by' => $record->replacement_id,
                                    'replace_at' => $record->replacement_at
                                ];
                                $replace_url = Signature::getUrl($replaceData);
                                $signData['replace'] = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&format=png&data={$replace_url}";
                            }

                            if ($record->known_by) {
                                $knownData = [
                                    'known_by' => $record->known_by,
                                    'known_at' => $record->known_at
                                ];
                                $known_url = Signature::getUrl($knownData);
                                $signData['known'] = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&format=png&data={$known_url}";
                            }

                            if (str_contains($record->status, 'Disetujui')) {
                                $approveData = [
                                    'approve_by' => $record->approver_id,
                                    'approve_at' => $record->approve_at
                                ];
                                $approve_url = Signature::getUrl($approveData);
                                $signData['approve'] = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&format=png&data={$approve_url}";
                            }

                            if ($record->is_verified) {
                                $verifiedData = [
                                    'verified_by' => $record->verified_by,
                                    'verified_at' => $record->verified_at
                                ];
                                $verified_url = Signature::getUrl($verifiedData);
                                $signData['verified'] = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&format=png&data={$verified_url}";
                            } 

                            $name = $record->staff->name ?? 'Pegawai';
                            $html = view('exports.leaves', [
                                'record' => $record,
                                'head' => $head,
                                'sdm' => $sdm,
                                'approver' => $approver,
                                'qrCode' => $signData,
                                'isWord' => true,
                            ])->render();

                            $fileName = $record->type . ' ' . $record->subtype . '_' . $name . '_' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . '.doc';

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
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
