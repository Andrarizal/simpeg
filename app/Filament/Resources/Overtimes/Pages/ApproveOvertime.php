<?php

namespace App\Filament\Resources\Overtimes\Pages;

use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Filament\Resources\Overtimes\Tables\ApproveTable;
use App\Models\Overtime;
use App\Models\Staff;
use Filament\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Mpdf\Mpdf;

class ApproveOvertime extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = OvertimeResource::class;

    protected string $view = 'filament.resources.overtimes.pages.approve-overtime';

    protected static ?string $title = 'Riwayat Lembur';

    public ?Staff $staff = null;

    protected function getHeaderActions(): array {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->action(function ($livewire) {
                    $month = $livewire->tableFilters['month_year']['value'] ?? now()->format('m-Y');

                    $data = Overtime::query()
                        ->with(['staff.chair', 'staff.unit'])
                        ->where('staff_id', $this->staff->id)
                        ->where('month_year', $month)
                        ->orderBy('overtime_date')
                        ->get();

                        
                    $head = Staff::select('name')->where('chair_id', $data[0]->staff->chair->head_id)->first()->name;
                    $sdm = Staff::whereHas('chair', fn ($q) => $q->where('name', 'like', '%SDM%'))->select('name')->with('chair')->first()->name;

                    $html = view('exports.overtimes', compact('data', 'month', 'head', 'sdm'))->render();

                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4-L',
                        'margin_left'   => 20, // 2.5 cm
                        'margin_right'  => 25, // 2 cm
                        'margin_top'    => 20, // 2.5 cm
                        'margin_bottom' => 25, // 2 cm
                    ]);

                    $mpdf->WriteHTML($html);

                    $pdfData = $mpdf->Output('', 'S');

                    return response()->streamDownload(function () use ($pdfData) {
                        echo $pdfData;
                    }, "rekap-lembur-$month.pdf");
                }),
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
}
