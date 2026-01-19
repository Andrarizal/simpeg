<?php

namespace App\Filament\Resources\Leaves\Schemas;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class LeaveInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['md' => 3, 'default' => 1])
                    ->schema([
                        Group::make([
                            Section::make(fn ($record) => $record->type . ' ' . $record->subtype)
                                ->extraAttributes(fn ($record) => [
                                    'class' => implode(' ', [
                                        '[&_.fi-section-header]:bg-gradient-to-br',
                                        '[&_.fi-section-header]:rounded-t-2xl',
                                        '[&_.fi-section-header-heading]:!text-white',
                                        '[&_.fi-section-header-description]:!text-white/80',
                                        '[&_.fi-section-header_.fi-icon-btn]:!text-white',

                                        ($record->type == 'Cuti') 
                                            ? '[&_.fi-section-header]:from-emerald-500 [&_.fi-section-header]:to-teal-600 [&_.fi-section-header]:dark:from-emerald-900 [&_.fi-section-header]:dark:to-teal-950'
                                            : '[&_.fi-section-header]:from-blue-400 [&_.fi-section-header]:to-sky-900 [&_.fi-section-header]:dark:from-blue-500 [&_.fi-section-header]:dark:to-sky-950',
                                    ])
                                ])
                                ->schema([
                                    TextEntry::make('date_range')
                                        ->hiddenLabel()
                                        ->state(fn ($record) => new HtmlString(
                                            '<div class="flex items-center gap-4">
                                                <div class="p-3 bg-primary-50 text-primary-600 rounded-xl border border-primary-100 dark:bg-primary-900/20 dark:border-primary-800">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm text-gray-500 font-medium">Periode Cuti</div>
                                                    <div class="text-2xl -mt-1 font-bold tracking-tight text-gray-900 dark:text-white">
                                                        ' . Carbon::parse($record->start_date)->translatedFormat('d M Y') . ' 
                                                        <span class="text-gray-300 mx-2 font-light">-</span> 
                                                        ' . Carbon::parse($record->end_date)->translatedFormat('d M Y') . '
                                                    </div>
                                                </div>
                                            </div>'
                                        )),
                                ]),
                            Grid::make(['md' => 2])
                                ->schema([
                                    Section::make('Rincian Pengajuan')
                                        ->icon('heroicon-m-document-text')
                                        ->schema([
                                            TextEntry::make('reason')
                                                ->label('Alasan / Keperluan')
                                                ->prose()
                                                ->markdown()
                                                ->columnSpanFull()
                                                ->extraAttributes([
                                                    'class' => '-mt-2'
                                                ]),

                                            TextEntry::make('replacement.name')
                                                ->label('Pegawai Pengganti')
                                                ->placeholder('-')
                                                ->formatStateUsing(fn ($state, $record) => $state . ' (' . match ($record->is_replaced) {
                                                    1 => 'Bersedia menggantikan',
                                                    0 => 'Tidak bersedia',
                                                    default => 'Menunggu konfirmasi',
                                                } . ')')
                                                ->extraAttributes([
                                                    'class' => '-mt-2'
                                                ]),

                                            TextEntry::make('evidence')
                                                ->label('Lampiran')
                                                ->visible(fn ($state) => $state ? true : false)
                                                ->formatStateUsing(fn ($state) => $state ? substr(basename($state), 0, 15) . "..." : null)
                                                ->icon('heroicon-m-arrow-down-tray')
                                                ->color('primary')
                                                ->suffixAction(
                                                    Action::make('view_evidence')
                                                        ->icon('heroicon-m-eye')
                                                        ->tooltip('Lihat Dokumen')
                                                        ->iconButton()
                                                        ->color('primary')
                                                        ->visible(fn ($record) => $record->evidence != null)
                                                        ->modalWidth('5xl') 
                                                        ->modalHeading(fn ($record) => "Preview Lampiran - " . $record->staff->name)
                                                        ->modalSubmitAction(false)
                                                        ->modalCancelAction(false)
                                                        ->modalContent(function ($record) {
                                                            return view('filament.components.preview-pdf-3', [
                                                                'url' => route('evidence.preview', ['record' => $record->id]),
                                                            ]);
                                                        })
                                                )
                                        ]),

                                    Section::make('Respon Atasan')
                                        ->icon('heroicon-m-chat-bubble-bottom-center-text')
                                        ->schema([
                                            TextEntry::make('approver.name')
                                                ->label(function ($record){
                                                    if (str_contains($record->status, 'Disetujui')){
                                                        if ($record->staff->chair->level < 4 && $record->status == 'Disetujui Kepala Seksi'){
                                                            return 'Diketahui Oleh';
                                                        }
                                                        return 'Disetujui Oleh';
                                                    } elseif (str_contains($record->status, 'Diketahui')){
                                                        return 'Diketahui Oleh';
                                                    } elseif (str_contains($record->status, 'Ditolak')){
                                                        return 'Ditolak Oleh';
                                                    }
                                                })
                                                ->visible(fn ($record) => $record->approver_id)
                                                ->weight(FontWeight::Bold)
                                                ->extraAttributes([
                                                    'class' => '-mt-2'
                                                ]),
                                            
                                            TextEntry::make('adverb')
                                                ->label('Catatan')
                                                ->visible(fn ($record) => $record->approver_id)
                                                ->markdown()
                                                ->placeholder('-')
                                                ->extraAttributes([
                                                    'class' => '-mt-2'
                                                ]),
                                        ]),
                                ]),
                        ])->columnSpan(['md' => 2]),
                        Group::make([
                            Section::make(function($record) {
                                    if ($record->status === 'Disetujui Kepala Seksi' && $record->staff->chair->level < 4){
                                        return 'Diketahui Kepala Seksi';
                                    } elseif ($record->status === 'Menunggu'){
                                        return 'Menunggu Persetujuan';
                                    }
                                    return $record->status;
                                })
                                ->extraAttributes(function ($record) {
                                    $html = '';
                                    if (str_contains($record->status, 'Menunggu')) {
                                        $html = '[&_.fi-section-header]:from-amber-400 [&_.fi-section-header]:to-yellow-900 [&_.fi-section-header]:dark:from-amber-500 [&_.fi-section-header]:dark:to-yellow-950';
                                    } elseif (str_contains($record->status, 'Diketahui') || ($record->status == 'Disetujui Kepala Seksi' && $record->staff->chair->level < 4)) {
                                        $html = '[&_.fi-section-header]:from-blue-400 [&_.fi-section-header]:to-sky-900 [&_.fi-section-header]:dark:from-blue-500 [&_.fi-section-header]:dark:to-sky-950';
                                    } elseif (str_contains($record->status, 'Disetujui')){
                                        $html = '[&_.fi-section-header]:from-emerald-500 [&_.fi-section-header]:to-teal-600 [&_.fi-section-header]:dark:from-emerald-900 [&_.fi-section-header]:dark:to-teal-950';
                                    } else {
                                        $html = '[&_.fi-section-header]:from-rose-400 [&_.fi-section-header]:to-red-900 [&_.fi-section-header]:dark:from-rose-500 [&_.fi-section-header]:dark:to-red-950';
                                    }
                                        
                                    return [
                                        'class' => implode(' ', [
                                            '[&_.fi-section-header]:bg-gradient-to-br',
                                            '[&_.fi-section-header]:rounded-2xl',
                                            '[&_.fi-section-header-heading]:!text-white',
                                            '[&_.fi-section-header-description]:!text-white/80',
                                            '[&_.fi-section-header_.fi-icon-btn]:!text-white', $html
                                            ])
                                        ];
                                    }),
                            Section::make(function($record) {
                                    if ($record->is_verified === 1){
                                        return 'Terverifikasi SDM';
                                    } elseif ($record->is_verified === 0){
                                        return 'Tidak Terverifikasi SDM';
                                    } else {
                                        return 'Menunggu Verifikasi SDM';
                                    }
                                })
                                ->extraAttributes(function ($record) {
                                    $html = '';
                                    if ($record->is_verified === 1) {
                                        $html = '[&_.fi-section-header]:from-emerald-500 [&_.fi-section-header]:to-teal-600 [&_.fi-section-header]:dark:from-emerald-900 [&_.fi-section-header]:dark:to-teal-950';
                                    } elseif ($record->is_verified === 0){
                                        $html = '[&_.fi-section-header]:from-rose-400 [&_.fi-section-header]:to-red-900 [&_.fi-section-header]:dark:from-rose-500 [&_.fi-section-header]:dark:to-red-950';
                                    } else {
                                        $html = '[&_.fi-section-header]:from-amber-400 [&_.fi-section-header]:to-yellow-900 [&_.fi-section-header]:dark:from-amber-500 [&_.fi-section-header]:dark:to-yellow-950';
                                    }
                                        
                                    return [
                                        'class' => implode(' ', [
                                            '[&_.fi-section-header]:bg-gradient-to-br',
                                            '[&_.fi-section-header]:rounded-2xl',
                                            '[&_.fi-section-header-heading]:!text-white',
                                            '[&_.fi-section-header-description]:!text-white/80',
                                            '[&_.fi-section-header_.fi-icon-btn]:!text-white', $html
                                            ])
                                        ];
                                    }),
                            Section::make('Pemohon')
                                ->schema([
                                    TextEntry::make('staff.name')
                                        ->hiddenLabel()
                                        ->formatStateUsing(function ($record) { 
                                            $staff = $record->staff;
                                            $imageUrl = null;
                                            $element = '';

                                            if ($staff->pas && asset('storage/' . $staff->pas)) {
                                                $imageUrl = asset('storage/' . $staff->pas);
                                            }

                                            $initials = collect(explode(' ', $staff->name))
                                                ->map(fn ($segment) => $segment[0] ?? '')
                                                ->take(2)
                                                ->join('');

                                            if ($imageUrl) {
                                                $element = '<div class="shrink-0 relative">
                                                        <img src="' . $imageUrl . '" 
                                                            alt="' . $staff->name . '" 
                                                            class="w-10 h-10 rounded-full object-cover border-4 border-white/20 shadow-md bg-gray-200">
                                                    </div>
                                                ';
                                            } else {
                                                $element = '<div class="shrink-0 relative">
                                                        <div class="w-10 h-10 rounded-full bg-gray-500 flex items-center justify-center text-sm font-bold text-white border-4 border-white/10 shadow-md">
                                                            ' . strtoupper($initials) . '
                                                        </div>
                                                    </div>
                                                ';
                                            }

                                        return new HtmlString(
                                            '<div class="flex items-center gap-3">
                                                ' . $element . '
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-gray-900 dark:text-gray-100 leading-tight">'.$record->staff->name.'</span>
                                                    <span class="text-xs text-gray-500">'.$record->staff->chair->name.'</span>
                                                </div>
                                            </div>');
                                    }),
                                    
                                    TextEntry::make('created_at')
                                        ->label('Diajukan pada')
                                        ->icon('heroicon-m-clock')
                                        ->date('d M Y, H:i')
                                        ->size(TextSize::Small)
                                        ->extraAttributes([
                                            'class' => '-mt-2'
                                        ]),
                                    
                                    Grid::make(2)->schema([
                                        TextEntry::make('leaves_taken')
                                            ->label('Cuti Terpakai')
                                            ->visible(fn ($record) => $record->type == 'Cuti')
                                            ->state(function ($record) {
                                                return $record
                                                    ->where('type', 'Cuti')
                                                    ->where('subtype', 'Tahunan')
                                                    ->where('status', '!=', 'Ditolak')
                                                    ->whereYear('start_date', now()->year)
                                                    ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1')) . ' Hari';
                                            })
                                            ->extraAttributes([
                                                'class' => '-mt-2'
                                            ])
                                            ->color('danger'),

                                        TextEntry::make('leaves_remaining')
                                            ->label('Sisa Cuti')
                                            ->visible(fn ($record) => $record->type == 'Cuti')
                                            ->state(function ($record) {
                                                $quota = setting('max_leave_days'); 
                                                
                                                $taken = $record
                                                    ->where('type', 'Cuti')
                                                    ->where('subtype', 'Tahunan')
                                                    ->where('status', '!=', 'Ditolak')
                                                    ->whereYear('start_date', now()->year)
                                                    ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1'));
                                                    
                                                return ($quota - $taken) . ' Hari';
                                            })
                                            ->extraAttributes([
                                                'class' => '-mt-2'
                                            ])
                                            ->weight(FontWeight::Bold)
                                            ->color('success'),
                                        TextEntry::make('permission_taken')
                                            ->label('Izin Terpakai')
                                            ->visible(fn ($record) => $record->type == 'Izin')
                                            ->state(function ($record) {
                                                return $record
                                                    ->where('type', 'Izin')
                                                    ->where('subtype', 'Non-Sakit')
                                                    ->where('status', '!=', 'Ditolak')
                                                    ->whereYear('start_date', now()->year)
                                                    ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1')) . ' Hari';
                                            })
                                            ->extraAttributes([
                                                'class' => '-mt-2'
                                            ])
                                            ->color('danger'),

                                        TextEntry::make('permission_remaining')
                                            ->label('Sisa Izin')
                                            ->visible(fn ($record) => $record->type == 'Izin')
                                            ->state(function ($record) {
                                                $quota = setting('max_permission_days'); 
                                                
                                                $taken = $record
                                                    ->where('type', 'Izin')
                                                    ->where('subtype', 'Non-Sakit')
                                                    ->where('status', '!=', 'Ditolak')
                                                    ->whereYear('start_date', now()->year)
                                                    ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1'));
                                                    
                                                return ($quota - $taken) . ' Hari';
                                            })
                                            ->extraAttributes([
                                                'class' => '-mt-2'
                                            ])
                                            ->weight(FontWeight::Bold)
                                            ->color('success'),
                                    ]),
                                ]),

                        ])->columnSpan(['md' => 1]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
