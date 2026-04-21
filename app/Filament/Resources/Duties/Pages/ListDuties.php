<?php

namespace App\Filament\Resources\Duties\Pages;

use App\Filament\Resources\Duties\DutyResource;
use App\Filament\Resources\Duties\Tables\DutiesTable;
use App\Filament\Resources\Duties\Tables\StaffsTable;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ListDuties extends ListRecords
{
    protected static string $resource = DutyResource::class;

    public ?string $pdfToken = null;
    
    public function mount(): void
    {
        $requestedTab = request()->query('tab') ?? request()->query('activeTab');

        if ($requestedTab) {
            $this->activeTab = $requestedTab;
        }

        if ($requestedTab === 'verifikasi') {
            $user = Auth::user();
            $isAdmin = $user->role_id != 4;

            if (! $isAdmin) {
                $this->redirect($this->getResource()::getUrl('index'));
                return;
            }
        }

        parent::mount();
    }

    public function getHeading(): string | Htmlable
    {
        return new HtmlString(<<<HTML
            <div class="flex items-center gap-x-2">
                <span>Penugasan</span>
                
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
            ->modalHeading('Panduan Penugasan')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalContent(fn () => new HtmlString('
                <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
                    <p>Berikut adalah panduan lengkap mengenai alur penugasan, notulensi hingga verifikasinya:</p>
                    
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 mb-2">A. Penugasan</h4>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Sekretariat membuatkan surat tugas melalui Tombol Buat Surat Tugas.</li>
                            <li>Tombol tersebut mengarah ke form yang harus diisi oleh Sekretariat terutama pada siapa saja tugas tersebut ditujukan.</li>
                            <li>Tombol Buat akan mengirimkan Notifikasi ke akun pegawai yang ditugaskan.</li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 mb-2">B. Notulensi</h4>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>
                                Pegawai mencetak surat tugasnya terlebih dahulu melalui tombol Lihat.
                            </li>
                            <li>
                                Pastikan surat tugas yang telah dicetak telah mendapatkan Cap atau Tanda Tangan di bagian Verifikasi Penyelenggara.
                            </li>
                            <li>
                                Pegawai mengakses Form Notulensi melalui QR Code yang ada pada surat tugas ataupun tombol notulensi yang ada pada menu Penugasan.
                            </li>
                            <li>
                                Pegawai mengisi Notulensi serta mengupload bukti berupa Foto Selfie, Materi (opsional), dan Surat Tugas yang sudah distempel dan discan.
                            </li>
                            <li>
                                Pegawai pilih opsi Ya atau Tidak pada pertanyaan terkait waktu kegiatan setelah pegawai klik tombol Simpan Laporan Notulensi.
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 mb-2">C. Verifikasi SDM</h4>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>SDM masuk ke bagian Perlu Verifikasi dan pilih opsi Lihat Laporan pada pegawai yang dimaksud.</li>
                            <li>SDM dapat melihat setiap bukti yang berhasil diupload pegawai dengan klik kotak "Sudah Upload" di masing-masing bukti.</li>
                            <li>SDM dapat melakukan verifikasi dengan klik opsi Respon dan pilih antara Verifikasi atau Tolak pada setiap bukti pegawai.</li>
                        </ul>
                    </div>
                </div>
            '));
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Buat Surat Tugas')
            ->visible(fn () => str_contains(Auth::user()->staff->chair->name, 'Sekretariat')),
        ];
    }

    public function getTabs(): array
    {
        $user = Auth::user();
        $user->staff_id = $user->staff_id ?? 1;

        $arrOfTabs = [];
        
        if ($user->staff->chair->level == 4 && $user->role_id == 1){
            $arrOfTabs['tugas'] = Tab::make('Tugas Anda')
                ->icon('heroicon-o-document-text');
            $arrOfTabs['verifikasi'] = Tab::make('Perlu Verifikasi')
                ->icon('heroicon-o-clipboard-document-check');
        }

        return $arrOfTabs;
    }

    public function getTable(): Table
    {
        $this->activeTab = $this->activeTab ?? 'tugas';
        if (Auth::user()->staff->chair->level < 4){
            $this->activeTab = "verifikasi";
        }
        $table = parent::getTable();

        if ($this->activeTab == 'tugas') {
            return DutiesTable::configure($table);
        }

        if ($this->activeTab == 'verifikasi') {
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
