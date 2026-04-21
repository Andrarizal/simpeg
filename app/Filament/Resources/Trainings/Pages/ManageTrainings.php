<?php

namespace App\Filament\Resources\Trainings\Pages;

use App\Filament\Resources\Trainings\TrainingResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ManageTrainings extends ManageRecords
{
    protected static string $resource = TrainingResource::class;

    public function getHeading(): string | Htmlable
    {
        return new HtmlString(<<<HTML
            <div class="flex items-center gap-x-2">
                <span>Pelatihan Pegawai</span>
                
                <button 
                    type="button" 
                    wire:click="mountAction('infoAction')" 
                    class="text-primary-500 hover:text-primary-600 transition focus:outline-none" 
                    title="Lihat Panduan Pelatihan Pegawai"
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
            ->modalHeading('Panduan Pelatihan Pegawai')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth('2xl')
            ->modalContent(fn () => new HtmlString('
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-4">
                    <p>Berikut adalah prosedur pencatatan riwayat pelatihan/diklat pegawai beserta proses verifikasinya:</p>
                    
                    <div class="rounded-lg border border-purple-200 bg-purple-50/50 p-4 dark:border-purple-800/50 dark:bg-purple-900/20">
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Staf Diklat masuk ke menu <strong>Pelatihan Pegawai</strong>.</li>
                            <li>Klik nama pegawai yang pelatihannya hendak diperiksa kelengkapannya.</li>
                            <li>Setelah meninjau bukti sertifikat dan kesesuaian data, Pilih Tindakan:
                                <ul class="list-none pl-2 mt-2 space-y-1">
                                    <li><span class="text-purple-600 dark:text-purple-400 font-medium">✓ Verifikasi:</span> Riwayat pelatihan diakui dan disahkan ke dalam sistem.</li>
                                    <li><span class="text-red-600 dark:text-red-400 font-medium">✗ Tolak:</span> Riwayat pelatihan ditolak (misalnya karena bukti tidak valid).</li>
                                </ul>
                            </li>
                            <li class="text-xs text-gray-500 mt-2">Apapun hasil keputusannya (Verifikasi/Tolak), sistem akan secara otomatis mengirimkan notifikasi kepada pegawai yang bersangkutan.</li>
                        </ul>
                    </div>

                </div>
            '));
    }
}
