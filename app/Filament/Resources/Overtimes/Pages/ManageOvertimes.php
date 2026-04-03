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
use Illuminate\Support\Facades\Auth;

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
