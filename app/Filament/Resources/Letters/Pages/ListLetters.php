<?php

namespace App\Filament\Resources\Letters\Pages;

use App\Filament\Resources\Letters\LetterResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ListLetters extends ListRecords
{
    protected static string $resource = LetterResource::class;

    public ?string $pdfToken = null;

    public function getHeading(): string | Htmlable
    {
        return new HtmlString(<<<HTML
            <div class="flex items-center gap-x-2">
                <span>Disposisi & Undangan</span>
                
                <button 
                    type="button" 
                    wire:click="mountAction('infoAction')" 
                    class="text-primary-500 hover:text-primary-600 transition focus:outline-none" 
                    title="Lihat Panduan Penugasan"
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
            ->modalHeading('Panduan Disposisi & Undangan')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth('4xl')
            ->modalContent(fn () => new HtmlString('
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    <p class="mb-4">Berikut adalah panduan alur pembuatan Surat Disposisi dan Undangan:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="space-y-4">
                            <div class="rounded-lg border border-indigo-200 bg-indigo-50/50 p-4 dark:border-indigo-800/50 dark:bg-indigo-900/20">
                                <h4 class="font-bold text-indigo-700 dark:text-indigo-400 mb-2 flex items-center gap-2">
                                    Alur Disposisi
                                </h4>
                                <p class="text-xs mb-3 text-gray-500 dark:text-gray-400">Pengelolaan terkait surat masuk dari eksternal rumah sakit.</p>
                                
                                <ul class="list-decimal pl-5 space-y-2">
                                    <li>Sekretariat mengakses menu Disposisi/Undangan.</li>
                                    <li>Klik tombol <span class="font-semibold">Keluarkan Surat</span>.</li>
                                    <li>Pilih opsi <span class="font-semibold text-indigo-600 dark:text-indigo-400">Disposisi</span>.</li>
                                    <li>Lengkapi Urgensi, No Agenda, Tgl, Keterangan, Nomor, Perihal, Asal dan Tanggal Surat.</li>
                                    <li>Unggah surat asli/surat masuk.</li>
                                    <li>Klik Buat untuk meneruskan ke bagian Umum & Kepegawaian</li>
                                    <br>
                                    <li>Bagian Umum & Kepegawian ke menu Disposisi/Undangan. Pada disposisi tersebut, pilih Action <span class="font-semibold">Tindaklanjuti</span>.</li>
                                    <li>Isi kolom instruksi dan pilih staf yang dituju/diberikan disposisi.</li>
                                    <li>Klik <span class="font-semibold">Buat</span>. Surat akan otomatis terdistribusi dan staf penerima akan mendapat Notifikasi.</li>
                                    <br>
                                    <li>Staf penerima mengakses menu Disposisi & Undangan.</li>
                                    <li>Klik Komentari pada Disposisi yang dimaksud untuk memberikan feedback.</li>
                                    <li>Isi Kolom Komentar dan Simpan untuk mempublikasikan komentar serta mengirim notifikasi ke Sekretariat dan Umum & Kepegawaian.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-lg border border-amber-200 bg-amber-50/50 p-4 dark:border-amber-800/50 dark:bg-amber-900/20">
                                <h4 class="font-bold text-amber-700 dark:text-amber-400 mb-2 flex items-center gap-2">
                                    Alur Undangan
                                </h4>
                                <p class="text-xs mb-3 text-gray-500 dark:text-gray-400">Penyelenggaraan kegiatan yang melibatkan internal rumah sakit.</p>
                                
                                <ul class="list-decimal pl-5 space-y-2">
                                    <li>Sekretariat mengakses menu Disposisi/Undangan.</li>
                                    <li>Klik tombol <span class="font-semibold">Keluarkan Surat</span>.</li>
                                    <li>Pilih opsi <span class="font-semibold text-amber-600 dark:text-amber-400">Undangan</span>.</li>
                                    <li><strong>Cek Template:</strong>
                                        <ul class="list-disc pl-5 mt-1 space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                            <li><strong>Jika Belum Ada:</strong> Batal dan kembali ke menu utama. Klik <span class="font-semibold">Kelola Template Undangan</span>, isi Nama Template, Salam, Alinea, Penutup, lalu klik <span class="font-semibold">Buat Template</span>. Ulangi proses dari awal.</li>
                                        </ul>
                                    </li>
                                    <li><strong>Isi Detail Acara:</strong> Jika Template sudah ada, isi Tgl/Waktu, Lokasi, Keterangan, Tipe Penerima, Staf yang mengetahui, dan Perihal Acara.</li>
                                    <li>(Opsional) Unggah dokumen tambahan jika ada.</li>
                                    <li>Pilih daftar staf yang akan diundang ke acara tersebut.</li>
                                    <li>Klik <span class="font-semibold">Buat/Simpan</span>. Undangan akan otomatis terdistribusi dan peserta akan mendapat Notifikasi.</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            '));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('template-manager')
                ->label('Kelola Template Undangan')
                ->modalHeading('Manajemen Template Surat')
                ->modalContent(view('filament.pages.partials.template-manager-modal')) 
                ->modalSubmitAction(false) 
                ->modalCancelAction(false)
                ->modalWidth('3xl')
                ->icon('heroicon-o-swatch')
                ->color('gray')
                ->visible(fn () => str_contains(Auth::user()->staff->chair->name, 'Sekretariat'))
                ->slideOver(),
            CreateAction::make()
                ->label('Keluarkan Surat')
                ->createAnother(true)
                ->visible(fn () => str_contains(Auth::user()->staff->chair->name, 'Sekretariat')),
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
