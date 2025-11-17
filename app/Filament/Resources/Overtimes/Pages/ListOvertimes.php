<?php

namespace App\Filament\Resources\Overtimes\Pages;

use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Filament\Resources\Overtimes\Tables\OvertimesTable;
use App\Filament\Resources\Overtimes\Tables\StaffsTable;
use App\Models\Overtime;
use App\Models\Staff;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;

class ListOvertimes extends ListRecords
{
    protected static string $resource = OvertimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->visible(fn () => $this->activeTab === 'pengajuan' ?? false)
                ->action(function ($livewire) {
                    $month = $livewire->tableFilters['month_year']['value'] ?? now()->format('m-Y');

                    $data = Overtime::query()
                        ->with(['staff.chair', 'staff.unit'])
                        ->where('staff_id', Auth::user()->staff_id)
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
        
        if (($user->staff->chair->level === 4 && $user->staff->unit->leader_id === $user->staff->chair_id) || $user->staff->chair->level === 4 && $user->role_id === 1){
            $arrOfTabs['pengajuan'] = Tab::make('Pengajuan Anda')
                ->icon('heroicon-o-document-text');
            $arrOfTabs['persetujuan'] = Tab::make('Perlu ' . ($user->role_id === 1 ? 'Verifikasi' : 'Persetujuan'))
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

        if ($this->activeTab === 'pengajuan') {
            return OvertimesTable::configure($table);
        }

        if ($this->activeTab === 'persetujuan') {
            return StaffsTable::configure($table);
        }

        return $table;
    }
}
