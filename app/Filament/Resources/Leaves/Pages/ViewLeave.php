<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Resources\Leaves\LeaveResource;
use App\Models\Leave;
use App\Models\Staff;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;

class ViewLeave extends ViewRecord
{
    protected static string $resource = LeaveResource::class;

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
                ->action(function ($record) {
                    $head = Staff::select('name')->where('chair_id', $record->staff->chair->head_id)->first()->name;
                    $sdm = Staff::whereHas('chair', fn ($q) => $q->where('name', 'like', '%SDM%'))->select('name')->with('chair')->first()->name;

                    $html = view('exports.leaves', compact('record', 'head', 'sdm'))->render();

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
                    }, 'permohonan-' . $record->type . '-' . $record->staff->name . '.pdf');
                }),
        ];
    }
}
