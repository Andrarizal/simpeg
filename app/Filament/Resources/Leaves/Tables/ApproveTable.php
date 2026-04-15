<?php

namespace App\Filament\Resources\Leaves\Tables;

use App\Filament\Pages\Signature;
use App\Filament\Resources\Leaves\LeaveResource;
use App\Models\Chair;
use App\Models\Leave;
use App\Models\MonthlyPeriod;
use App\Models\Staff;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
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

class ApproveTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = Leave::query();
                
                // Jika SDM
                if (Auth::user()->role_id == 1){
                    $query->orderBy('created_at', 'DESC');
                    // JIka Bukan SDM
                } else {
                    // Jika Kanit
                    $query->where('staff_id', '!=', Auth::user()->staff_id); // Buang cuti milik sendiri
                    if (Auth::user()->staff->chair->level == 4){
                        $query->whereHas('staff.chair', function ($q) {
                            // Ambil yang satu struktur kepengurusan (Koor User Cuti == Koor Kanit)
                            $q->where('head_id', Auth::user()->staff->chair->head_id);
                            // Ambil yang selevel (Karyawan)
                            $q->where('level', Auth::user()->staff->chair->level);
                        });
                    // Jika lebih tinggi dari Kanit
                    } else if (Auth::user()->staff->chair->level != 1) {
                        // Masukkan id dari atasan (langsung) user cuti ke array heads
                        $heads = Leave::with(['staff.chair', 'approver.chair'])
                                ->get()
                                ->map(function ($leave) {
                                    return [$leave->staff->chair->head_id];
                                })
                                ->toArray();
                                
                        foreach($heads as &$head){
                            // Cek apabila atasan yang ada di head bukan direktur
                            while (!in_array(null, $head)){
                                // Kumpulkan semua atasan dari user cuti
                                $head[] = Chair::where('id', end($head))->first()->head_id;
                            }
                        }
                        unset($head);
                        
                        $matchFound = false;
                        foreach ($heads as $head){
                            // Jika terdapat user login yang sesuai dengan salah satu heads
                            if(in_array(Auth::user()->staff->chair_id, $head)){
                                $matchFound = true;
                                // Ambil yang memiliki level di bawahnya
                                $query->whereHas('staff.chair', function ($q) use ($head){
                                    $q->whereIn('head_id', $head)
                                    ->where('level', '>', Auth::user()->staff->chair->level);
                                });
                                break;
                            }
                        }

                        // Jika User login tidak sesuai dengan heads
                        if (!$matchFound) {
                            $query->whereRaw('1 = 0'); // Paksa hasil kosong
                        }
                    }
                }
                return $query->orderBy('created_at', 'DESC');
            })
            ->columns([
                TextColumn::make('staff.name')
                    ->label('Nama Pengaju')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->formatStateUsing(fn ($record) => $record->type . ' ' . $record->subtype)
                    ->label('Jenis'),
                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->alignCenter()
                    ->date(),
                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->alignCenter()
                    ->date(),
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
                TextColumn::make('remaining')
                    ->label('Sisa Cuti')
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
                    ->label(fn ($record) => Auth::user()->staff->chair->level > 2 || (Auth::user()->staff->chair->level == 2 && $record->staff->chair->level == 3) ? 'Rekomendasi' : 'Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => shouldShowApprovalButton($record))
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('adverb')
                            ->label('Catatan/Rekomendasi')
                            ->rows(3),
                    ])
                    ->action(function (array $data, $record) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;
                        $staff = $user->staff;

                        $level = $staff->chair->level;

                        $role = '';
                        $verb = '';
                        $notifColor = 'success';

                        switch ($level) {
                            case 4:
                                $role = 'Kepala Unit';
                                $verb = 'diketahui';
                                $notifColor = 'info';
                                break;
                            case 3:
                                $role = 'Koordinator';
                                $verb = 'diketahui';
                                $notifColor = 'info';
                                break;
                            case 2:
                                $role = 'Kepala Seksi';
                                $verb = 'disetujui';
                                $notifColor = 'success';
                                break;
                            case 1:
                                $role = 'Direktur';
                                $verb = 'disetujui';
                                $notifColor = 'success';
                                break;
                        }

                        if (!empty($role)) {
                            $updateData = [
                                'status'      => ucfirst($verb) . ' ' . $role,
                                'approver_id' => $staff->id,
                                'approve_at'  => Carbon::now(),
                                'adverb'      => $data['adverb']
                            ];

                            if ($level == 3 || $level == 4) {
                                $updateData['known_by'] = $staff->id;
                                $updateData['known_at'] = Carbon::now();
                            } 
                            elseif ($level == 2 && $record->staff->chair->level == 3) {
                                $updateData['known_by'] = $staff->id;
                                $updateData['known_at'] = Carbon::now();
                            }

                            $record->update($updateData);

                            Notification::make()
                                ->title($record->type . ' Anda telah ' . $verb . ' ' . $role)
                                ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah ' . $verb . ' ' . $role)
                                ->status($notifColor)
                                ->actions([
                                    Action::make('read')
                                        ->label('Lihat')
                                        ->button()
                                        ->url(LeaveResource::getUrl('index'))
                                        ->markAsRead(),
                                ])
                                ->sendToDatabase($record->staff->user);

                            $isSdmRoute = ($level == 1 || ($record->staff->chair->level == 4 && $level == 2));

                            if ($isSdmRoute) {
                                $staffs = Staff::with('user')->whereHas('chair', fn ($q) => $q->where('name', 'like', '%SDM%'))->get();
                                
                                $usersToNotify = $staffs->pluck('user')->filter(); 
                                $actionType = 'Verifikasi';
                            } else {
                                $head = $record->staff->chair->parent->staff->first();
                                
                                $usersToNotify = collect([$head?->user])->filter(); 
                                $actionType = 'Persetujuan';
                            }

                            if ($usersToNotify->isNotEmpty()) {
                                Notification::make()
                                    ->title("{$record->type} menunggu {$actionType}")
                                    ->body("{$record->staff->name} telah mengajukan {$record->type} pada tanggal " . Carbon::parse($record->start_date)->translatedFormat('d F Y'))
                                    ->warning()
                                    ->actions([
                                        Action::make('review')
                                            ->label('Tinjau')
                                            ->url(LeaveResource::getUrl('view', ['record' => $record]))
                                            ->markAsRead(),
                                    ])
                                    ->sendToDatabase($usersToNotify);
                            }

                            Notification::make()
                                ->title($record->type . ' ' . ucfirst($verb))
                                ->success()
                                ->send();
                        }
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn ($record) => shouldShowApprovalButton($record))
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('adverb')
                            ->label('Alasan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (array $data, $record) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;
                        $staff = $user->staff;

                        $record->update([
                            'status' => 'Ditolak',
                            'approver_id' => $staff->id,
                            'approve_at' => Carbon::now(),
                            'adverb' => $data['adverb']
                        ]);

                        $level = $staff->chair->level;

                        $role = '';

                        switch ($level) {
                            case 4:
                                $role = 'Kepala Unit';
                                break;
                            case 3:
                                $role = 'Koordinator';
                                break;
                            case 2:
                                $role = 'Kepala Seksi';
                                break;
                            case 1:
                                $role = 'Direktur';
                                break;
                        }

                        Notification::make()
                            ->title($record->type . ' Anda telah ditolak oleh ' . $role)
                            ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah ditolak dengan alasan: ' . $data['adverb'])
                            ->danger()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(LeaveResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title($record->type . ' ditolak')
                            ->success()
                            ->send();
                    }),
                Action::make('verified')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(function ($record) {
                        return Auth::user()->role_id == 1 
                            && is_null($record->is_verified)
                            && ($record->staff->chair->level == 4 ? $record->status == 'Disetujui Kepala Seksi' : $record->status == 'Disetujui Direktur')
                            && $record->status != 'Ditolak';
                    })
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_verified' => 1,
                            'verified_by' => Auth::user()->staff_id,
                            'verified_at' => Carbon::now()
                        ]);

                        Notification::make()
                            ->title($record->type . ' Anda telah diverifikasi SDM')
                            ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah diverifikasi SDM')
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(LeaveResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title($record->type . ' diverifikasi')
                            ->success()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(function ($record) {
                        return Auth::user()->role_id == 1 
                            && is_null($record->is_verified)
                            && ($record->staff->chair->level == 4 ? $record->status == 'Disetujui Kepala Seksi' : $record->status == 'Disetujui Direktur')
                            && $record->status != 'Ditolak';
                    })
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('adverb')
                            ->label('Alasan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (array $data, $record) {
                        $record->update([
                            'is_verified' => 0,
                            'adverb' => $data['adverb']
                        ]);

                        Notification::make()
                            ->title($record->type . ' Anda telah dibatalkan SDM')
                            ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah dibatalkan SDM dengan alasan: ' . $data['adverb'])
                            ->danger()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(LeaveResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title($record->type . ' dibatalkan')
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
