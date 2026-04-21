<?php

namespace App\Filament\Resources\ContractEvaluations\Pages;

use App\Filament\Resources\ContractEvaluations\ContractEvaluationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ManageContractEvaluations extends ManageRecords
{
    protected static string $resource = ContractEvaluationResource::class;

    public ?string $pdfToken = null;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getHeading(): string | Htmlable
    {
        return new HtmlString(<<<HTML
            <div class="flex items-center gap-x-2">
                <span>Evaluasi Kontrak</span>
                
                <button 
                    type="button" 
                    wire:click="mountAction('infoAction')" 
                    class="text-primary-500 hover:text-primary-600 transition focus:outline-none" 
                    title="Lihat Panduan Evaluasi Kontrak"
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
            ->modalHeading('Panduan Evaluasi Kontrak')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalContent(fn () => new HtmlString('
                <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
                    <p>Berikut adalah panduan lengkap mengenai alur evaluasi kontrak pegawai:</p>
                    
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 mb-2">A. Pra-Evaluasi</h4>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Kontrak Pegawai dapat dievaluasi apabila telah memenuhi 2 periode atau semester dari penilaian kinerja.</li>
                            <li>Nilai Akhir diambil berdasarkan rata-rata dari nilai final masing-masing semester Penilaian Kinerja, oleh karena itu.</li>
                            <li>Evaluasi baru dapat dilakukan apabila pegawai telah dinilai pada masing-masing semester Penilaian Kinerja oleh tim Assesor.</li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 mb-2">B. Tata Alur Evaluasi Kontrak</h4>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>
                                <strong>Staf SDM:</strong><br>
                                Memiliki wewenang untuk melakukan evaluasi kontrak berdasarkan penilaian kinerja melalui tombol Evaluasi pada Kontrak Pegawai yang secara otomatis menentukan Nilai Akhir dan Kelulusan berdasarkan KKM yang telah diatur di Konfigurasi Sistem.
                            </li>
                            <li>
                                <strong>Kepala Sub Bagian Tata Usaha:</strong><br>
                                Memberikan catatan tambahan yang dapat berguna bagi evaluasi melalui tombol Beri Catatan pada Kontrak Pegawai. Fitur ini dapat diakses apabila kontrak telah dievaluasi oleh SDM.
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 mb-2">C. Paska Evaluasi</h4>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Bagi Pegawai yang kontraknya di telah selesai dievaluasi akan menerima notifikasi serta dapat mengakses print out hasil evaluasi melalui tombol Export Data di menu Evaluasi Kontrak.</li>
                            <li>Sementara bagi SDM, Hasil dari Evaluasi Kontrak mengharuskan segera terkait keputusan akhir untuk memperpanjang ataupun menyudahi Kontrak dari Pegawai yang bersangkutan.</li>
                        </ul>
                    </div>
                </div>
            '));
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
