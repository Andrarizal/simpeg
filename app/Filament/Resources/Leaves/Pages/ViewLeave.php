<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Pages\Signature;
use App\Filament\Resources\Leaves\LeaveResource;
use App\Models\Leave;
use App\Models\Staff;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ViewLeave extends ViewRecord
{
    protected static string $resource = LeaveResource::class;

    public ?string $pdfToken = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label(fn ($record) => Auth::user()->staff->chair->level > 2 || (Auth::user()->staff->chair->level == 2 && $record->staff->chair->level == 3) ? 'Ketahui' : 'Setujui')
                ->icon('heroicon-o-check')
                ->visible(fn ($record) => shouldShowApprovalButton($record)) // Pakai helpers custom untuk atur visibilitas antar role
                ->requiresConfirmation()
                ->action(function ($record) {
                    $user = Auth::user();
                    $user->staff_id = $user->staff_id ?? 1;

                    // Cek level jabatan dari user login
                    switch ($user->staff->chair->level) {
                        case 4:
                            $record->update([
                                'status' => 'Diketahui Kepala Unit',
                                'approver_id' => $user->staff_id
                            ]);
                            break;
                        case 3:
                            $record->update([
                                'status' => 'Diketahui Koordinator',
                                'approver_id' => $user->staff_id
                            ]);
                            break;
                        case 2:
                            $record->update([
                                'status' => 'Disetujui Kepala Seksi',
                                'approver_id' => $user->staff_id
                            ]);
                            break;
                        case 1:
                            $record->update([
                                'status' => 'Disetujui Direktur',
                                'approver_id' => $user->staff_id
                            ]);
                            break;
                    }
                }),
            Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(fn ($record) => shouldShowApprovalButton($record)) // Pakai helpers custom untuk atur visibilitas antar role
                ->requiresConfirmation()
                ->action(function ($record) {
                    $user = Auth::user();
                    $user->staff_id = $user->staff_id ?? 1;

                    $record->update([
                        'status' => 'Ditolak',
                        'approver_id' => $user->staff_id
                    ]);
                }),
            Action::make('verified')
                ->label('Verifikasi')
                ->icon('heroicon-o-check')
                ->color('info')
                ->visible(function ($record) {
                    if (Auth::user()->role_id === 1) {
                        return $record->is_verified ? false : true;
                    }
                    return false;
                })
                // ->visible(true)
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update([
                        'is_verified' => 1,
                    ]);
                }),
            Action::make('cancel')
                ->label('Batalkan')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(function ($record) {
                    if (Auth::user()->role_id === 1) {
                        return $record->is_verified ? false : true;
                    }
                    return false;
                })
                // ->visible(true)
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update([
                        'is_verified' => 0,
                    ]);
                }),
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->visible(function ($record) {
                    if (Auth::user()->staff_id === $record->staff_id) {
                        return true;
                    }
                    return false;
                })
                ->modalHeading('Preview Cuti')
                ->modalWidth('5xl')
                ->modalContent(function ($record) {
                    $head = Staff::where('chair_id', $record->staff->chair->head_id)->first();
                    $sdm = Staff::whereHas('chair', fn ($q) => $q->where('name', 'like', '%SDM%'))->select('name')->with('chair')->first()->name;

                    $approver = '';
                    if ($record->staff->chair->level === 4){
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
                        'qrCode' => $signData
                    ])->render();

                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'margin_left'   => 25, // 2.5 cm
                        'margin_right'  => 20, // 2 cm
                        'margin_top'    => 25, // 2.5 cm
                        'margin_bottom' => 20, // 2 cm
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
                ->closeModalByEscaping(false),
        ];
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
}
