<?php

namespace App\Filament\Resources\Overtimes\Pages;

use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Filament\Resources\Overtimes\Schemas\OvertimeInfolist;
use App\Filament\Resources\Overtimes\Tables\OvertimesTable;
use App\Filament\Resources\Overtimes\Tables\StaffsTable;
use App\Models\Staff;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ManageOvertimes extends ManageRecords
{
    protected static string $resource = OvertimeResource::class;

    public ?string $pdfToken = null;
    public ?bool $verified = true;
    public ?bool $known = true;

    public function mount(): void
    {
        $requestedTab = request()->query('tab') ?? request()->query('activeTab');

        if ($requestedTab) {
            $this->activeTab = $requestedTab;
        }

        if ($requestedTab === 'persetujuan') {
            $user = Auth::user();
            $isBoss = $user->staff->chair->level != 4 || $user->staff->unit->leader_id == $user->staff->chair_id;

            if (! $isBoss) {
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
                <span>Lembur</span>
                
                <button 
                    type="button" 
                    wire:click="mountAction('infoAction')" 
                    class="text-primary-500 hover:text-primary-600 transition focus:outline-none" 
                    title="Lihat Panduan Lembur"
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
            ->modalHeading('Panduan Pengajuan dan Verifikasi Lembur')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalContent(fn () => new HtmlString('
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-4">
                <p>Berikut adalah prosedur alur pengajuan lembur hingga proses verifikasinya:</p>
                
                <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-800/50 dark:bg-emerald-900/20">
                    <h4 class="font-bold text-emerald-700 dark:text-emerald-400 mb-2 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-200 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200 text-xs">1</span>
                        Pengajuan oleh Karyawan
                    </h4>
                    <ul class="list-disc pl-5 mt-2 space-y-1">
                        <li>Karyawan masuk ke menu <strong>Lembur</strong>. Pastikan Anda memiliki jadwal kerja pada hari tersebut.</li>
                        <li>Klik tombol <strong>Ajukan Lembur</strong>.</li>
                        <li>Isi formulir pengajuan: masukkan Tanggal & Tugas. <em>(Waktu mulai akan terisi otomatis, biarkan Waktu Selesai kosong)</em>.</li>
                        <li>Klik <strong>Buat</strong>. Status lembur Anda kini menunggu untuk diketahui oleh Atasan.</li>
                        <li>Apabila telah menyelesaikan lembur, klik tombol <strong>Selesai</strong> untuk mencatat Jam Selesai dan menghitung total jam.</li>
                    </ul>
                </div>

                <div class="rounded-lg border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-800/50 dark:bg-blue-900/20">
                    <h4 class="font-bold text-blue-700 dark:text-blue-400 mb-2 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-200 text-blue-800 dark:bg-blue-800 dark:text-blue-200 text-xs">2</span>
                        Persetujuan Kepala Unit / Koordinator
                    </h4>
                    <ul class="list-disc pl-5 mt-2 space-y-1">
                        <li>Atasan masuk ke menu <strong>Lembur</strong>, lalu buka tab <strong>Perlu Persetujuan</strong>.</li>
                        <li>Pilih action <strong>Lihat Lembur</strong> pada nama karyawan yang mengajukan.</li>
                        <li>Gunakan <em>Filter Periode Lembur</em> jika diperlukan.</li>
                        <li>Pilih Tindakan:
                            <ul class="list-none pl-2 mt-1 space-y-1">
                                <li><span class="text-blue-600 dark:text-blue-400 font-medium">✓ Ketahui:</span> Menyetujui lembur untuk diteruskan ke SDM.</li>
                                <li><span class="text-red-600 dark:text-red-400 font-medium">✗ Tolak:</span> Menolak lembur dengan wajib mengisi alasan penolakan.</li>
                            </ul>
                        </li>
                        <li class="text-xs text-gray-500 mt-1">Sistem akan otomatis mengirimkan notifikasi ke karyawan terkait keputusan ini.</li>
                    </ul>
                </div>

                <div class="rounded-lg border border-purple-200 bg-purple-50/50 p-4 dark:border-purple-800/50 dark:bg-purple-900/20">
                    <h4 class="font-bold text-purple-700 dark:text-purple-400 mb-2 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-purple-200 text-purple-800 dark:bg-purple-800 dark:text-purple-200 text-xs">3</span>
                        Verifikasi Akhir oleh SDM
                    </h4>
                    <ul class="list-disc pl-5 mt-2 space-y-1">
                        <li>SDM masuk ke menu <strong>Lembur</strong>, lalu buka tab <strong>Perlu Verifikasi</strong>.</li>
                        <li>Pilih action <strong>Lihat Lembur</strong> pada nama karyawan yang sudah disetujui atasannya.</li>
                        <li>Gunakan <em>Filter Periode Lembur</em> jika diperlukan.</li>
                        <li>Pilih Tindakan:
                            <ul class="list-none pl-2 mt-1 space-y-1">
                                <li><span class="text-purple-600 dark:text-purple-400 font-medium">✓ Verifikasi:</span> Lembur disahkan secara resmi.</li>
                                <li><span class="text-red-600 dark:text-red-400 font-medium">✗ Batalkan:</span> Lembur dibatalkan oleh SDM dengan wajib menyertakan alasan.</li>
                            </ul>
                        </li>
                        <li class="text-xs text-gray-500 mt-1">Sistem akan otomatis mengirimkan notifikasi hasil akhir ke karyawan yang bersangkutan.</li>
                    </ul>
                </div>

            </div>
            '));
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajukan Lembur')
                ->visible(function () {
                    if (Auth::user()->staff->chair->level < 4) return false;
                    if ($this->activeTab != 'pengajuan') return false;

                    return true;
                })
                ->after(function ($record) {
                    $sender = Auth::user();
                    $senderStaff = $sender->staff;

                    if ($senderStaff){
                        $unitStaff = $senderStaff->unit ?? null;
                        $recipientUser = null;

                        if (!$unitStaff->leader_id || $unitStaff->leader_id == $senderStaff->id) {
                            $recipientUser = $senderStaff->chair->parent->staff->first()->user ?? null;
                        } else {
                            $recipientUser = $unitStaff->leader->staff->first()->user ?? null;
                        }
    
                        if ($recipientUser) {
                            Notification::make()
                                ->title('Pengajuan Lembur Baru')
                                ->body("{$senderStaff->name} mengajukan lembur pada tanggal " . Carbon::parse($record->overtime_date)->format('d F Y'))
                                ->icon('heroicon-o-clock')
                                ->iconColor('warning')
                                ->actions([
                                    Action::make('Lihat')
                                        ->button()
                                        ->url(OvertimeResource::getUrl('approve', ['record' => $senderStaff->id])),
                                ])
                                ->sendToDatabase($recipientUser);
                        }
                    }

                    $sdms = Staff::with('user')->whereHas('chair', fn ($q) => $q->where('name', 'like', '%SDM%'))->get();
                    $usersToNotify = $sdms->pluck('user')->filter(); 

                    Notification::make()
                        ->title('Pengajuan Lembur Baru')
                        ->body("{$senderStaff->name} mengajukan lembur pada tanggal " . Carbon::parse($record->overtime_date)->format('d F Y'))
                        ->icon('heroicon-o-clock')
                        ->iconColor('warning')
                        ->actions([
                            Action::make('Lihat')
                                ->button()
                                ->url(OvertimeResource::getUrl('approve', ['record' => $record])),
                        ])
                        ->sendToDatabase($usersToNotify);
                }),
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

    public function getTabs(): array
    {
        $user = Auth::user();
        $user->staff_id = $user->staff_id ?? 1;

        $arrOfTabs = [];
        
        if (($user->staff->chair->level == 4 && $user->staff->unit->leader_id == $user->staff->chair_id) || $user->staff->chair->level == 4 && $user->role_id == 1){
            $arrOfTabs['pengajuan'] = Tab::make('Pengajuan Anda')
                ->icon('heroicon-o-document-text');
            $arrOfTabs['persetujuan'] = Tab::make('Perlu ' . ($user->role_id == 1 ? 'Verifikasi' : 'Persetujuan'))
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

        if ($this->activeTab == 'pengajuan') {
            return OvertimesTable::configure($table);
        }

        if ($this->activeTab == 'persetujuan') {
            return StaffsTable::configure($table);
        }

        return $table;
    }

    public function infolist(Schema $schema): Schema
    {
        return OvertimeInfolist::configure($schema);
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
