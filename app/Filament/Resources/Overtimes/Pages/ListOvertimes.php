<?php

namespace App\Filament\Resources\Overtimes\Pages;

use App\Filament\Pages\Signature;
use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Filament\Resources\Overtimes\Tables\OvertimesTable;
use App\Filament\Resources\Overtimes\Tables\StaffsTable;
use App\Models\Overtime;
use App\Models\Staff;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ListOvertimes extends ListRecords
{
    protected static string $resource = OvertimeResource::class;

    public ?string $pdfToken = null;
    public ?bool $verified = true;
    public ?bool $known = true;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->visible(fn () => $this->activeTab == 'pengajuan' ?? false)
                ->modalHeading('Preview Cuti')
                ->modalWidth('5xl')
                ->modalContent(function ($livewire) {
                    $month = $livewire->tableFilters['month_year']['value'] ?? now()->format('m-Y');

                    $data = Overtime::query()
                        ->with(['staff.chair', 'staff.unit'])
                        ->where('staff_id', Auth::user()->staff_id)
                        ->where('month_year', $month)
                        ->orderBy('overtime_date')
                        ->get();

                    if ($data->isEmpty()) {
                        Notification::make()
                            ->title('Tidak ada data lembur di bulan ini')
                            ->warning()
                            ->send();
                        return; 
                    }
                    
                    $head = Staff::select('name')->where('chair_id', $data[0]->staff->chair->head_id)->first()->name;

                    if (!$head) {
                        Notification::make()
                            ->title('Atasan user belum dipilih!')
                            ->danger()
                            ->send();
                        return; 
                    }
                    
                    $sdm = Staff::whereHas('chair', fn ($q) => $q->where('name', 'like', '%SDM%'))->select('name')->with('chair')->first()->name;

                    if (!$sdm) {
                        Notification::make()
                            ->title('Belum ada data untuk posisi SDM!')
                            ->danger()
                            ->send();
                        return; 
                    }

                    foreach ($data as $i => $p) {
                        $this->verified = $p->is_verified ?? false;
                        $this->known = $p->is_known == 2 ?? false;
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
                        'month' => $month,
                        'head' => $head,
                        'sdm' => $sdm,
                        'qrCode' => $signData
                    ])->render();

                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4-L',
                        'margin_left'   => 20, // 2.5 cm
                        'margin_right'  => 25, // 2 cm
                        'margin_top'    => 20, // 2.5 cm
                        'margin_bottom' => 25, // 2 cm
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
            CreateAction::make()
                ->label('Ajukan Lembur')
                ->visible(function () {
                    if (Auth::user()->staff->chair->level < 4) return false;
                    if ($this->activeTab != 'pengajuan') return false;

                    return true;
                }),
        ];
    }

    public function getTabs(): array
    {
        $user = Auth::user();
        $user->staff_id = $user->staff_id ?? 1;

        $arrOfTabs = [];
        
        if (($user->staff->chair->level == 4 && $user->staff->unit->leader_id == $user->staff->chair_id) || $user->staff->chair->level == 4 && $user->role_id == 1){
            $arrOfTabs['pengajuan'] = Tab::make('Pengajuan Anda')
                ->icon('heroicon-o-document-text');
            $arrOfTabs['persetujuan'] = Tab::make('Perlu ' . ($user->role_id == 1 ? 'Verifikasi' : 'Persetujuan'))
                ->icon('heroicon-o-clipboard-document-check');
        }

        return $arrOfTabs;
    }

    public function getTable(): Table
    {
        $this->activeTab = $this->activeTab ?? 'pengajuan';
        if (Auth::user()->staff->chair->level < 4){
            $this->activeTab = "persetujuan";
        }
        $table = parent::getTable();

        if ($this->activeTab == 'pengajuan') {
            return OvertimesTable::configure($table);
        }

        if ($this->activeTab == 'persetujuan') {
            return StaffsTable::configure($table);
        }

        return $table;
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
