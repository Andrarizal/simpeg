<?php

namespace App\Filament\Resources\Overtimes\Pages;

use App\Filament\Pages\Signature;
use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Filament\Resources\Overtimes\Tables\ApproveTable;
use App\Models\MonthlyPeriod;
use App\Models\Overtime;
use App\Models\Staff;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Notifications\Notification;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ApproveOvertime extends Page implements HasTable, HasInfolists
{
    use InteractsWithTable;
    use InteractsWithInfolists;

    protected static string $resource = OvertimeResource::class;

    protected string $view = 'filament.resources.overtimes.pages.approve-overtime';

    protected static ?string $title = 'Riwayat Lembur';

    public ?Staff $staff = null;
    public ?string $pdfToken = null;
    public ?bool $verified = true;
    public ?bool $known = true;

    protected function getHeaderActions(): array {
        return [
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
                            ->where('staff_id', $this->staff->id)
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
                        $sdm = Staff::select('name')->whereHas('chair', fn ($q) => $q->where('name', 'like', '%SDM%'))->first()?->name;

                        if (!$head) {
                            return view('filament.components.alert', [
                                'message' => 'Atasan user belum dipilih! Tidak dapat melanjutkan proses.',
                                'color'   => 'danger',
                            ]);
                        }

                        foreach ($data as $i => $p) {
                            if (!$p->is_verified) {
                                $this->verified = false;
                                break;
                            }
                        }

                        foreach ($data as $i => $p) {
                            if ($p->is_known != 2) {
                                $this->known = false;
                                break;
                            }
                        }

                        $signData = [
                            'known' => null,
                            'verified' => null,
                        ];

                        if ($this->known) {
                            $knownData = [
                                'known_by' => $data[0]['known_by'],
                                'known_at' => $data[0]['known_at']
                            ];
                            $known_url = Signature::getUrl($knownData);
                            $signData['known'] = base64_encode(QrCode::format('svg')->size(100)->generate($known_url));
                        } 

                        if ($this->verified) {
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
                            'known' => $this->known,
                            'verified' => $this->verified,
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

                        $this->pdfToken = $token;

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
                            ->where('staff_id', $this->staff->id)
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
                                $this->verified = false;
                                break;
                            }
                        }

                        foreach ($data as $i => $p) {
                            if ($p->is_known != 2) {
                                $this->known = false;
                                break;
                            }
                        }

                        $signData = [
                            'known' => null,
                            'verified' => null,
                        ];

                        if ($this->known) {
                            $knownData = [
                                'known_by' => $data[0]['known_by'],
                                'known_at' => $data[0]['known_at']
                            ];
                            $known_url = Signature::getUrl($knownData);
                            $signData['known'] = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&format=png&data={$known_url}";
                        } 

                        if ($this->verified) {
                            $verifiedData = [
                                'verified_by' => $data[0]['verified_by'],
                                'verified_at' => $data[0]['verified_at']
                            ];
                            $verified_url = Signature::getUrl($verifiedData);
                            $signData['verified'] = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&format=png&data={$verified_url}";
                        } 

                        $name = Staff::find($this->staff->id)->name ?? 'Pegawai';
                        $html = view('exports.overtimes', [
                            'data' => $data,
                            'month' => $period->name,
                            'head' => $head,
                            'sdm' => $sdm,
                            'qrCode' => $signData,
                            'known' => $this->known,
                            'verified' => $this->verified,
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
        ];
    } 

    public function table(Table $table): Table
    {
        return ApproveTable::configure($table, $this->staff);
    }
    
    public function mount(int|string $record): void
    {
        $this->staff = Staff::findOrFail($record);
    }
    
    public function closePreviewAndCleanup() {
        if ($this->pdfToken) {
            $path = storage_path("app/private/livewire-tmp/{$this->pdfToken}.pdf");
            if (file_exists($path)) {
                @unlink($path);
            }
            $this->pdfToken = null;
        }

        $this->unmountAction();
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();
        if (!$user || !$user->staff || !$user->staff->chair) {
            return false; 
        }

        return $user->staff->chair->level != 4 
            || $user->staff->unit->leader_id == $user->staff->chair_id || $user->role_id == 1;
    }
}
