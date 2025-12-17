<?php

namespace App\Filament\Resources\Overtimes\Pages;

use App\Filament\Pages\Signature;
use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Filament\Resources\Overtimes\Tables\ApproveTable;
use App\Models\Overtime;
use App\Models\Staff;
use Filament\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ApproveOvertime extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = OvertimeResource::class;

    protected string $view = 'filament.resources.overtimes.pages.approve-overtime';

    protected static ?string $title = 'Riwayat Lembur';

    public ?Staff $staff = null;
    public ?string $pdfToken = null;
    public ?bool $verified = true;
    public ?bool $known = true;

    protected function getHeaderActions(): array {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->modalHeading('Preview Cuti')
                ->modalWidth('5xl')
                ->modalContent(function ($livewire) {
                    $month = $livewire->tableFilters['month_year']['value'] ?? now()->format('m-Y');

                    $data = Overtime::query()
                        ->with(['staff.chair', 'staff.unit'])
                        ->where('staff_id', $this->staff->id)
                        ->where('month_year', $month)
                        ->orderBy('overtime_date')
                        ->get();

                        
                    $head = Staff::select('name')->where('chair_id', $data[0]->staff->chair->head_id)->first()->name;
                    $sdm = Staff::whereHas('chair', fn ($q) => $q->where('name', 'like', '%SDM%'))->select('name')->with('chair')->first()->name;

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
}
