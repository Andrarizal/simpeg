<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Resources\Leaves\LeaveResource;
use App\Filament\Resources\Leaves\Tables\ApproveTable;
use App\Filament\Resources\Leaves\Tables\LeavesTable;
use App\Filament\Resources\Leaves\Tables\ReplacerTable;
use App\Models\Leave;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    public ?string $pdfToken = null;

    public function mount(): void
    {
        $requestedTab = request()->query('tab') ?? request()->query('activeTab');

        if ($requestedTab === 'persetujuan') {
            $user = Auth::user();
            $isBoss = $user->staff->chair->level != 4 || $user->staff->unit->leader_id == $user->staff->chair_id;

            if (! $isBoss) {
                $this->redirect($this->getResource()::getUrl('index'));
                return;
            }
        }

        Leave::query()
            ->whereDate('start_date', '<', Carbon::now()->toDateString())
            ->where(function ($query) {
                $query->where('status', '!=', 'Disetujui Kepala Seksi')
                      ->orWhere('status', '!=', 'Disetujui Direktur')
                      ->orWhereNull('is_verified');
            })->delete();
        parent::mount();
    }

    public function getHeading(): string | Htmlable
    {
        return new HtmlString(<<<HTML
            <div class="flex items-center gap-x-2">
                <span>Cuti / Izin</span>
                
                <button 
                    type="button" 
                    wire:click="mountAction('infoApproval')" 
                    class="text-primary-500 hover:text-primary-600 transition focus:outline-none" 
                    title="Lihat Panduan Approval"
                >
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </button>
            </div>
        HTML);
    }

    public function infoApprovalAction(): Action
    {
        return Action::make('infoApproval')
            ->modalHeading('Panduan Jenjang Approval Cuti & Izin')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalContent(fn () => new HtmlString('
                <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
                    <p>Berikut adalah panduan lengkap mengenai jenjang persetujuan dan verifikasi dokumen cuti/izin:</p>
                    
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 mb-2">A. Syarat Pra-Persetujuan (Khusus Shift)</h4>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Bagi pegawai dengan sistem kerja <strong>Shift</strong>, dokumen permohonan baru bisa diproses oleh atasan jika sudah ada Pegawai Pengganti yang dikonfirmasi.</li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 mb-2">B. Alur Persetujuan Struktural</h4>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>
                                <strong>Tingkat 1 (Kepala Unit / Kepala Ruangan):</strong><br>
                                Bagi pemohon dari unit yang dipimpin oleh kepala unit, maka cuti harus diketahui oleh kepala unit terlebih dahulu.
                            </li>
                            <li>
                                <strong>Tingkat 2 (Koordinator):</strong><br>
                                Menyetujui permohonan staf yang berada di bawah unit yang ia pimpin termasuk kepala unit jika mengajukan. Khusus pegawai fungsional yang dipimpin oleh Kepala Unit, permohonan harus berstatus <em>Diketahui Kepala Unit</em> terlebih dahulu.
                            </li>
                            <li>
                                <strong>Tingkat 3 (Kepala Seksi):</strong><br>
                                Menyetujui permohonan dari pejabat tingkat Koordinator, atau permohonan pegawai unit yang telah diteruskan oleh Koordinator.
                            </li>
                            <li>
                                <strong>Tingkat 4 (Direktur):</strong><br>
                                Menyetujui permohonan langsung dari Kepala Seksi, atau permohonan lain yang telah melewati Kepala Seksi.
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 mb-2">C. Verifikasi Akhir (SDM / Kepegawaian)</h4>
                        <p class="mb-2">Proses validasi dan rekapitulasi akhir oleh Bagian Kepegawaian dilakukan jika dokumen telah memenuhi syarat berikut:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Bagi pemohon dari staf fungsional/pelaksana: Dokumen wajib berstatus <strong>Disetujui Kepala Seksi</strong>.</li>
                            <li>Bagi pemohon dari pejabat struktural (manajerial): Dokumen wajib berstatus <strong>Disetujui Direktur</strong>.</li>
                        </ul>
                    </div>
                </div>
            '));
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajukan Cuti / Izin')
                ->hidden(fn () => Auth::user()->staff->chair->level == 1),
        ];
    }
 
    public function getTabs(): array
    {
        $user = Auth::user();
        $user->staff_id = $user->staff_id ?? 1;

        $arrOfTabs = [];
        
        if ($user->staff->chair->level != 1){
            if ($user->staff->unit->work_system == 'Shift' || ($user->staff->chair->level < 4 && $user->staff->unit->work_system == 'Tetap') || $user->role_id == 1) {
                $arrOfTabs['pengajuan'] = Tab::make('Pengajuan Anda')->icon('heroicon-o-document-text');
            }
            if ($user->staff->unit->work_system == 'Shift') {
                $arrOfTabs['pengganti'] = Tab::make('Pengganti')->icon('heroicon-o-document-arrow-up');
            }
        }

        if ($user->staff->chair->level != 4 || $user->role_id == 1 || $user->staff->unit->leader_id == $user->staff->chair_id){
            $arrOfTabs['persetujuan'] = Tab::make($user->role_id == 1 ? 'Perlu Verifikasi' : 'Perlu Persetujuan')->icon('heroicon-o-clipboard-document-check');
        }

        return $arrOfTabs;
    }

    public function getTable(): Table
    {
        $activeTab = $this->activeTab ?? 'pengajuan';
        $table = parent::getTable();

        if ($activeTab == 'pengajuan') {
            return LeavesTable::configure($table);
        }

        if ($activeTab == 'pengganti') {
            return ReplacerTable::configure($table);
        }

        if ($activeTab == 'persetujuan') {
            return ApproveTable::configure($table);
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
