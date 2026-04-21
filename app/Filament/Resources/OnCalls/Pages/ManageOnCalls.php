<?php

namespace App\Filament\Resources\OnCalls\Pages;

use App\Filament\Resources\OnCalls\OnCallResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ManageOnCalls extends ManageRecords
{
    protected static string $resource = OnCallResource::class;

    public function getHeading(): string | Htmlable
    {
        return new HtmlString(<<<HTML
            <div class="flex items-center gap-x-2">
                <span>On Call</span>
                
                <button 
                    type="button" 
                    wire:click="mountAction('infoAction')" 
                    class="text-primary-500 hover:text-primary-600 transition focus:outline-none" 
                    title="Lihat Panduan On Call"
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
            ->modalHeading('Panduan Perintah & Verifikasi On Call')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth('2xl')
            ->modalContent(fn () => new HtmlString('
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-4">
                    <p>Berikut adalah prosedur alur pemberian perintah On Call hingga proses verifikasi akhirnya:</p>
                    
                    <div class="rounded-lg border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-800/50 dark:bg-blue-900/20">
                        <h4 class="font-bold text-blue-700 dark:text-blue-400 mb-2 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-200 text-blue-800 dark:bg-blue-800 dark:text-blue-200 text-xs">1</span>
                            Pemberian Perintah oleh Atasan
                        </h4>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Atasan masuk ke menu <strong>On Call</strong>.</li>
                            <li>Klik tombol <strong>Perintahkan On Call</strong>.</li>
                            <li>Isi formulir penugasan: masukkan Tanggal, Tugas, Nama Pegawai yang ditunjuk, & Waktu Mulai.</li>
                            <li>Klik <strong>Buat</strong>. Sistem akan otomatis mengirimkan notifikasi kepada karyawan yang diperintah.</li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-800/50 dark:bg-emerald-900/20">
                        <h4 class="font-bold text-emerald-700 dark:text-emerald-400 mb-2 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-200 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200 text-xs">2</span>
                            Pelaksanaan oleh Karyawan
                        </h4>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Karyawan yang menerima notifikasi masuk ke menu <strong>On Call</strong>.</li>
                            <li>Gunakan <em>Filter Periode On Call</em> untuk melihat detail perintah tersebut.</li>
                            <li>Karyawan menjalankan tugas On Call sesuai instruksi.</li>
                            <li>Setelah tugas selesai, klik action <strong>Selesai</strong> pada data terkait. Status kini menunggu diketahui Atasan.</li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-indigo-200 bg-indigo-50/50 p-4 dark:border-indigo-800/50 dark:bg-indigo-900/20">
                        <h4 class="font-bold text-indigo-700 dark:text-indigo-400 mb-2 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-200 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200 text-xs">3</span>
                            Pengecekan Kembali oleh Atasan
                        </h4>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Atasan masuk kembali ke menu <strong>On Call</strong> dan atur <em>Filter Periode Lembur/On Call</em>.</li>
                            <li>Cari laporan karyawan yang telah terakumulasi total jamnya, lalu Pilih Tindakan:
                                <ul class="list-none pl-2 mt-1 space-y-1">
                                    <li><span class="text-indigo-600 dark:text-indigo-400 font-medium">✓ Ketahui:</span> Menyetujui penyelesaian tugas untuk diteruskan ke SDM.</li>
                                    <li><span class="text-red-600 dark:text-red-400 font-medium">✗ Tolak:</span> Menolak laporan penyelesaian dengan wajib menyertakan alasan penolakan.</li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-purple-200 bg-purple-50/50 p-4 dark:border-purple-800/50 dark:bg-purple-900/20">
                        <h4 class="font-bold text-purple-700 dark:text-purple-400 mb-2 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-purple-200 text-purple-800 dark:bg-purple-800 dark:text-purple-200 text-xs">4</span>
                            Verifikasi Akhir oleh Admin / SDM
                        </h4>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Admin/SDM masuk ke menu <strong>On Call</strong>.</li>
                            <li>Pastikan menggunakan <em>Filter Periode Lembur</em> & mengaktifkan <strong>Mode Tampilan Verifikasi SDM</strong>.</li>
                            <li>Pilih Tindakan akhir untuk data yang sudah disetujui Atasan:
                                <ul class="list-none pl-2 mt-1 space-y-1">
                                    <li><span class="text-purple-600 dark:text-purple-400 font-medium">✓ Verifikasi:</span> Tugas On Call disahkan secara resmi oleh SDM.</li>
                                    <li><span class="text-red-600 dark:text-red-400 font-medium">✗ Batalkan:</span> On Call dibatalkan dengan wajib menyertakan alasan.</li>
                                </ul>
                            </li>
                            <li class="text-xs text-gray-500 mt-1">Apapun keputusan SDM (Verifikasi/Batal), sistem akan mengirimkan notifikasi hasil akhirnya ke karyawan.</li>
                        </ul>
                    </div>

                </div>
            '));
    }
    
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Perintahkan On Call')
                ->visible(fn () => !OnCallResource::isSubordinate()),
            Action::make('periods')
                ->label('Kelola Periode')
                ->modalHeading('Manajemen Periode Bulanan')
                ->modalContent(view('filament.pages.partials.monthly-period-manager-modal')) 
                ->modalSubmitAction(false) 
                ->modalCancelAction(false)
                ->modalWidth('xl')
                ->icon('heroicon-o-swatch')
                ->color('gray')
                ->visible(fn() => Auth::user()->role_id == 1)
                ->slideOver(),
        ];
    }
}
