<?php

namespace App\Filament\Resources\Letters\Tables;

use App\Exports\DispositionExport;
use App\Filament\Pages\Signature;
use App\Filament\Resources\Letters\LetterResource;
use App\Models\Letter;
use App\Models\LetterReceiver;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $chairName = Auth::user()->staff->chair->name ?? '';
            
                if (str_contains($chairName, 'Sekretariat')) {
                    return $query->latest();
                }
            
                $query->where(function (Builder $subQuery) use ($chairName) {
                    $letters = LetterReceiver::where('staff_id', Auth::user()->staff_id)
                        ->pluck('letter_id')
                        ->toArray();
                    $subQuery->whereIn('id', $letters);
                    if (str_contains($chairName, 'Umum & Kepegawaian')) {
                        $subQuery->orWhere('classification', 'Disposisi');
                    }
                });
            
                return $query->latest();
            })
            ->headerActions([
                Action::make('exportResume')
                    ->label('Ekspor Resume')
                    ->icon('heroicon-o-document-arrow-down')
                    ->visible(fn ($livewire) => $livewire->tableFilters['classification'] && $livewire->tableFilters['classification']['value'] == 'Disposisi')
                    ->color('success')
                    ->action(function ($livewire) {
                        $year = $livewire->tableFilters['filter_year']['value'] ?? now()->year; 

                        return Excel::download(
                            new DispositionExport($year), 
                            "RESUME_DISPOSISI_{$year}.xlsx"
                        );
                    })
            ])
            ->columns([
                TextColumn::make('classification')
                    ->label('Jenis'),
                TextColumn::make('agenda_number')
                    ->label('Nomor')
                    ->hidden(fn ($livewire) => isset($livewire->tableFilters['classification']) && $livewire->tableFilters['classification']['value'] === 'Undangan'),
                TextColumn::make('sender')
                    ->label('Asal Surat')
                    ->default('Sekretariat')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Acara/Perihal')
                    ->searchable()
                    ->wrap()
                    ->formatStateUsing(fn (string $state): string => substr(nl2br(e($state)), 0, 100) . (strlen(nl2br(e($state))) > 100 ? '...' : ''))
                    ->html()
                    ->extraAttributes([
                        'class' => 'min-w-xs', 
                    ]),
                TextColumn::make('created_at')
                    ->label(fn ($livewire) => isset($livewire->tableFilters['classification']) && $livewire->tableFilters['classification']['value'] === 'Disposisi' ? 'Tanggal Diterima' : 'Tanggal Surat')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('agenda_date')
                    ->label('Tanggal Agenda')
                    ->state(fn ($record) => $record->end_date ? Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' - ' . Carbon::parse($record->end_date)->translatedFormat('d F Y') : Carbon::parse($record->start_date)->translatedFormat('d F Y'))
                    ->wrap()
                    ->hidden(fn ($livewire) => isset($livewire->tableFilters['classification']) && $livewire->tableFilters['classification']['value'] === 'Disposisi')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('classification')
                    ->label('Klasifikasi')
                    ->options([
                        'Disposisi' => 'Disposisi',
                        'Undangan' => 'Undangan',
                    ])
                    ->default('Disposisi')
                    ->indicateUsing(function (array $data) {
                        if (empty($data['value'])) return [];

                        return [
                            Indicator::make($data['value'])
                            ->removable(false),
                        ];
                    })
                    ->selectablePlaceholder(false)
                    ->native(false),
                SelectFilter::make('filter_year')
                    ->label('Tahun')
                    ->options(function () {
                        return Letter::query()
                            ->selectRaw('YEAR(created_at) as year') 
                            ->distinct() 
                            ->orderBy('year', 'desc') 
                            ->pluck('year', 'year'); 
                    })
                    ->indicateUsing(function (array $data) {
                        return [
                            Indicator::make($data['value'])
                                ->removable(false),
                        ];
                    })
                    ->default(now()->year)
                    ->selectablePlaceholder(false)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {   
                            $query->whereYear('created_at', $data['value']);
                        }
                    }),
            ])
            ->recordUrl(null)
            ->recordAction(fn ($record) => ($record->classification === 'Undangan' ? 'previewInvitation' : ($record->targetStaffs->pluck('id')->contains(Auth::user()->staff_id) ? 'addComment' : 'viewComment')))
            ->recordActions([
                Action::make('previewDisposition')
                    ->label('Lihat')
                    ->visible(fn ($record) => $record->classification === 'Disposisi')
                    ->icon('heroicon-o-eye') 
                    ->color('info')
                    ->modalHeading(false)
                    ->modalWidth('5xl') 
                    ->modalContent(function ($record, $livewire) {
                        $knownData = [
                            'known_by' => $record['known_by'],
                            'known_at' => $record['created_at']
                        ];
                        $known_url = Signature::getUrl($knownData);
                        $known = base64_encode(QrCode::format('svg')->size(48)->generate($known_url));

                        $html = view('exports.disposition', ['record' => $record, 'known' => $known])->render();

                        $mpdf = new Mpdf([
                            'mode' => 'utf-8', 
                            'format' => 'A4',
                            'margin_top' => 10,
                            'margin_left' => 15,
                            'margin_right' => 15,
                            'margin_bottom' => 10,
                        ]);

                        $mpdf->WriteHTML($html);

                        if (!empty($record->file_path)) {
                            $pathFileAsli = storage_path('app/public/' . $record->file_path);
                        
                            if (is_file($pathFileAsli)) {
                                try {
                                    $tmpDirectory = storage_path("app/private/livewire-tmp");
                                    if (!File::isDirectory($tmpDirectory)) {
                                        File::makeDirectory($tmpDirectory, 0755, true, true);
                                    }
                                    
                                    $pathFileDowngrade = $tmpDirectory . '/safe_' . basename($pathFileAsli);
                        
                                    $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . escapeshellarg($pathFileDowngrade) . " " . escapeshellarg($pathFileAsli);
                                    
                                    exec($command, $output, $returnCode);
                        
                                    $fileToProcess = ($returnCode === 0 && file_exists($pathFileDowngrade)) ? $pathFileDowngrade : $pathFileAsli;
                        
                                    $pageCount = $mpdf->SetSourceFile($fileToProcess);
                        
                                    for ($i = 1; $i <= $pageCount; $i++) {
                                        $mpdf->AddPage(); 
                                        $tplId = $mpdf->ImportPage($i);
                                        $mpdf->UseTemplate($tplId, ['adjustPageSize' => true]); 
                                    }
                        
                                    if (file_exists($pathFileDowngrade)) {
                                        unlink($pathFileDowngrade);
                                    }
                        
                                } catch (\Exception $e) {
                                    Notification::make()
                                        ->title('Gagal memuat lampiran PDF')
                                        ->body('Versi PDF tidak didukung. Silakan gunakan fitur Print to PDF.')
                                        ->danger()
                                        ->send();
                                }
                            }
                        }

                        $token = Str::uuid()->toString();
                        
                        $tempPath = storage_path("app/private/livewire-tmp/$token.pdf");
                        
                        file_put_contents($tempPath, $mpdf->Output('', 'S'));

                        if (property_exists($livewire, 'pdfToken')) {
                            $livewire->pdfToken = $token;
                        }

                        return view('filament.components.preview-pdf', [
                            'token' => $token,
                        ]);
                    })
                    ->modalHeading(false)
                    ->modalCancelAction(false)
                    ->modalSubmitAction(false)
                    ->modalCloseButton(false)
                    ->closeModalByClickingAway(false)
                    ->closeModalByEscaping(false),
                Action::make('previewInvitation')
                    ->label('Lihat')
                    ->visible(fn ($record) => $record->classification === 'Undangan')
                    ->icon('heroicon-o-eye') 
                    ->color('info')
                    ->modalHeading(false)
                    ->modalWidth('5xl') 
                    ->modalContent(function ($record, $livewire) {
                        $knownData = [
                            'known_by' => $record['known_by'],
                            'known_at' => $record['created_at']
                        ];
                        $known_url = Signature::getUrl($knownData);
                        $known = base64_encode(QrCode::format('svg')->size(100)->generate($known_url));

                        $html = view('exports.invitation', ['record' => $record, 'known' => $known])->render();

                        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                        $fontDirs = $defaultConfig['fontDir'];

                        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                        $fontData = $defaultFontConfig['fontdata'];

                        $mpdf = new Mpdf([
                            'mode' => 'utf-8', 
                            'format' => [215.9, 342.9],
                            'fontDir' => array_merge($fontDirs, [
                                public_path('fonts'), 
                            ]),
                            'fontdata' => $fontData + [
                                'tnr' => [
                                    'R' => 'times.ttf',    
                                    'B' => 'timesbd.ttf',  
                                    'I' => 'timesi.ttf',   
                                    'BI' => 'timesbi.ttf',  
                                ]
                            ],
                            'default_font' => 'tnr',
                            'margin_top' => 15,
                            'margin_left' => 20,
                            'margin_right' => 20,
                            'margin_bottom' => 15,
                        ]);

                        $mpdf->WriteHTML($html);

                        if (!empty($record->file_path)) {
                            $pathFileAsli = storage_path('app/public/' . $record->file_path);
                        
                            if (is_file($pathFileAsli)) {
                                try {
                                    $tmpDirectory = storage_path("app/private/livewire-tmp");
                                    if (!File::isDirectory($tmpDirectory)) {
                                        File::makeDirectory($tmpDirectory, 0755, true, true);
                                    }
                                    
                                    $pathFileDowngrade = $tmpDirectory . '/safe_' . basename($pathFileAsli);
                        
                                    $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . escapeshellarg($pathFileDowngrade) . " " . escapeshellarg($pathFileAsli);
                                    
                                    exec($command, $output, $returnCode);
                        
                                    $fileToProcess = ($returnCode === 0 && file_exists($pathFileDowngrade)) ? $pathFileDowngrade : $pathFileAsli;
                        
                                    $pageCount = $mpdf->SetSourceFile($fileToProcess);
                        
                                    for ($i = 1; $i <= $pageCount; $i++) {
                                        $mpdf->AddPage(); 
                                        $tplId = $mpdf->ImportPage($i);
                                        $mpdf->UseTemplate($tplId, ['adjustPageSize' => true]); 
                                    }
                        
                                    if (file_exists($pathFileDowngrade)) {
                                        unlink($pathFileDowngrade);
                                    }
                        
                                } catch (\Exception $e) {
                                    Notification::make()
                                        ->title('Gagal memuat lampiran PDF')
                                        ->body('Versi PDF tidak didukung. Silakan gunakan fitur Print to PDF.')
                                        ->danger()
                                        ->send();
                                }
                            }
                        }

                        $token = Str::uuid()->toString();
                        
                        $tempPath = storage_path("app/private/livewire-tmp/$token.pdf");
                        
                        file_put_contents($tempPath, $mpdf->Output('', 'S'));

                        if (property_exists($livewire, 'pdfToken')) {
                            $livewire->pdfToken = $token;
                        }

                        return view('filament.components.preview-pdf', [
                            'token' => $token,
                        ]);
                    })
                    ->modalHeading(false)
                    ->modalCancelAction(false)
                    ->modalSubmitAction(false)
                    ->modalCloseButton(false)
                    ->closeModalByClickingAway(false)
                    ->closeModalByEscaping(false),
                Action::make('viewComment')
                    ->label('Komentar')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->color('warning')
                    ->visible(function ($record) {
                        return !$record->targetStaffs->pluck('id')->contains(Auth::user()->staff_id) && $record->classification == 'Disposisi';
                    })
                    ->modalHeading('Lihat Komentar Disposisi')
                    ->modalWidth('lg')
                    ->modalSubmitAction(false)
                    ->modalContent(function($record){
                        $staffsWithComments = $record->targetStaffs()
                            ->wherePivotNotNull('comment')
                            ->orderByPivot('updated_at', 'asc')
                            ->get();

                        $html = '<div class="space-y-3 max-h-60 overflow-y-auto pr-2">';

                        foreach ($staffsWithComments as $staff) {
                            $nama = e($staff->name);
                            $waktu = Carbon::parse($staff->pivot->updated_at)->translatedFormat('d M Y, H:i');
                            $komentar = nl2br(e($staff->pivot->comment));

                            $html .= "
                                <div class='p-3 bg-gray-50 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10'>
                                    <div class='flex justify-between items-start'>
                                        <span class='font-semibold text-xs text-gray-900 dark:text-white'>{$nama}</span>
                                        <span class='text-xs text-gray-500'>Diupdate: {$waktu}</span>
                                    </div>
                                    <p class='text-sm text-gray-700 dark:text-gray-300 mb-0 -mt-4'>{$komentar}</p>
                                </div>
                            ";
                        }

                        $html .= '</div>';

                        return new HtmlString($html);
                    }),
                Action::make('addComment')
                    ->label('Komentari')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->color('warning')
                    ->visible(function ($record) {
                        return $record->targetStaffs->pluck('id')->contains(Auth::user()->staff_id) && $record->classification == 'Disposisi';
                    })
                    ->modalHeading('Input Komentar Disposisi')
                    ->modalWidth('lg')
                    ->schema([
                        TextEntry::make('comments')
                            ->label('Komentar Sebelumnya')
                            ->hidden(function ($record) {
                                return $record->targetStaffs()
                                    ->wherePivotNotNull('comment')
                                    ->doesntExist();
                            })
                            ->state(function ($record) {
                                $staffsWithComments = $record->targetStaffs()
                                    ->wherePivotNotNull('comment')
                                    ->orderByPivot('updated_at', 'asc')
                                    ->get();

                                $html = '<div class="space-y-3 max-h-60 overflow-y-auto pr-2">';

                                foreach ($staffsWithComments as $staff) {
                                    $nama = e($staff->name);
                                    $waktu = Carbon::parse($staff->pivot->updated_at)->translatedFormat('d M Y, H:i');
                                    $komentar = nl2br(e($staff->pivot->comment));

                                    $html .= "
                                        <div class='p-3 bg-gray-50 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10'>
                                            <div class='flex justify-between items-start'>
                                                <span class='font-semibold text-xs text-gray-900 dark:text-white'>{$nama}</span>
                                                <span class='text-xs text-gray-500'>Diupdate: {$waktu}</span>
                                            </div>
                                            <p class='text-sm text-gray-700 dark:text-gray-300 mb-0 -mt-4'>{$komentar}</p>
                                        </div>
                                    ";
                                }

                                $html .= '</div>';

                                return new HtmlString($html);
                            }),
                        Textarea::make('comment')
                            ->label('Komentar Anda')
                            ->placeholder('Tuliskan tanggapan atau laporan penyelesaian...')
                            ->required()
                            ->rows(5),
                    ])
                    ->action(function (array $data, $record) {
                        $user = Auth::user();
                        $staffName = $user->staff->name;
                        $comment = $data['comment'];

                        $receiver = LetterReceiver::where('letter_id', $record->id)
                            ->where('staff_id', $user->staff_id)->first();
                        $receiver->comment = $receiver->comment . "\n" . $comment;
                        $receiver->save();

                        $staffs = Staff::whereHas('chair', function ($query) {
                            $query->where('name', 'like', '%Sekretariat%')
                                ->orWhere('name', 'like', '%Umum & Kepegawaian%');
                        })->get();
                        
                        $recipientUsers = User::whereIn('staff_id', $staffs->pluck('id'))->get();

                        Notification::make()
                            ->title('Komentar Disposisi Baru')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->body("{$staffName} memberikan komentar pada Disposisi No. {$record->agenda_number}: \n{$comment}")
                            ->actions([
                                Action::make('view')
                                    ->label('Lihat')
                                    ->url(LetterResource::getUrl('index'))
                                    ->button(),
                            ])
                            ->sendToDatabase($recipientUsers);

                        Notification::make()
                            ->title('Komentar berhasil ditambahkan')
                            ->success()
                            ->send();
                    }),
                Action::make('outline')
                    ->label('Notulensi')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->url(fn ($record): string => route('filament.admin.resources.letters.outline', ['record' => $record]))
                    ->visible(fn ($record) => $record->receiver()->where('staff_id', Auth::user()->staff_id)->exists() && $record->classification === 'Undangan'),
                EditAction::make()
                    ->label(fn () => (str_contains(Auth::user()->staff->chair->name, 'Umum & Kepegawaian')) ? 'Tindaklanjuti' : 'Edit')
                    ->visible(fn () => str_contains(Auth::user()->staff->chair->name, 'Sekretariat') || (str_contains(Auth::user()->staff->chair->name, 'Umum & Kepegawaian'))),
                DeleteAction::make()
                    ->visible(fn () => str_contains(Auth::user()->staff->chair->name, 'Sekretariat'))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => str_contains(Auth::user()->staff->chair->name, 'Sekretariat')),
                ]),
            ]);
    }
}
