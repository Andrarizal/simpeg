<?php

namespace App\Filament\Resources\ShiftExchanges\Pages;

use App\Exports\ShiftExchangeExport;
use App\Filament\Resources\ShiftExchanges\ShiftExchangeResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class ManageShiftExchanges extends ManageRecords
{
    protected static string $resource = ShiftExchangeResource::class;

    public function getHeading(): string | Htmlable
    {
        return new HtmlString(<<<HTML
            <div class="flex items-center gap-x-2">
                <span>Tukar Jadwal</span>
                
                <button 
                    type="button" 
                    wire:click="mountAction('infoAction')" 
                    class="text-primary-500 hover:text-primary-600 transition focus:outline-none" 
                    title="Lihat Panduan Tukar Jadwal"
                >
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </button>
            </div>
        HTML);
    }

    public function infoAction(): Action
    {
        return Action::make('info')
            ->modalHeading('Panduan Tukar Jadwal')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth('3xl')
            ->modalContent(fn () => new HtmlString('
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-4">
                    <p>Berikut adalah prosedur persetujuan penukaran jadwal kerja antar pegawai:</p>
                    
                    <div class="rounded-lg border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-800/50 dark:bg-blue-900/20">
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Atasan masuk ke menu <strong>Tukar Jadwal</strong> untuk melihat daftar permintaan.</li>
                            <li>Pilih aksi <strong>Setujui/Tukar</strong> pada baris permintaan pertukaran yang masuk.</li>
                            <li><strong>Keputusan - Setujui:</strong> Jadwal kedua pegawai akan otomatis tertukar di dalam sistem, dan notifikasi akan terkirim ke pegawai pengaju beserta rekan penggantinya.</li>
                            <li><strong>Keputusan - Tolak:</strong> Jadwal kedua pegawai tetap tidak berubah. Notifikasi penolakan hanya akan dikirimkan kepada pegawai pengaju.</li>
                        </ul>
                    </div>

                </div>
            '));
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Ekspor Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function ($livewire) {
                    $staff = Auth::user()->staff;

                    $unit = $staff->unit->name; 
                    $periode = $livewire->tableFilters['month_year']['value'];

                    return Excel::download(
                        new ShiftExchangeExport($unit, $periode), 
                        "Rekap_Tukar_Jadwal_{$unit}_Periode_" . Carbon::parse($periode)->translatedFormat('F Y') . ".xlsx"
                    );
                })
        ];
    }
}
