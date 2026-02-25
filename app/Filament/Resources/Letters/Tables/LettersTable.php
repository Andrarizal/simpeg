<?php

namespace App\Filament\Resources\Letters\Tables;

use App\Models\LetterReceiver;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class LettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                if (str_contains(Auth::user()->staff->chair->name, 'Sekretariat')){
                    return $query->latest();
                } else {
                    $letters = LetterReceiver::where('staff_id', Auth::user()->staff_id)->pluck('letter_id')->toArray();
                    foreach ($letters as $letterId) {
                        $query->orWhere('id', $letterId);
                    }
                    return $query->latest();
                }
            })
            ->columns([
                TextColumn::make('classification')
                    ->label('Jenis'),
                TextColumn::make('sender')
                    ->label('Pengirim')
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
                    ->label('Tanggal Distribusi')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('agenda_date')
                    ->label('Tanggal Agenda')
                    ->date('d F Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('previewDisposition')
                    ->label('Lihat Disposisi')
                    ->icon('heroicon-o-eye') 
                    ->color('info')
                    ->modalHeading(false)
                    ->modalWidth('5xl') 
                    ->modalContent(function ($record, $livewire) {
                        $html = view('exports.disposition', ['record' => $record])->render();

                        $mpdf = new Mpdf([
                            'mode' => 'utf-8', 
                            'format' => 'A4',
                            'margin_top' => 10,
                            'margin_left' => 15,
                            'margin_right' => 15,
                            'margin_bottom' => 10,
                        ]);

                        $mpdf->WriteHTML($html);

                        $pathFileAsli = storage_path('app/public/' . $record->file_path);

                        if (file_exists($pathFileAsli)) {
                            try {
                                $pageCount = $mpdf->SetSourceFile($pathFileAsli);

                                for ($i = 1; $i <= $pageCount; $i++) {
                                    $mpdf->AddPage(); 
                                    $tplId = $mpdf->ImportPage($i);
                                    $mpdf->UseTemplate($tplId, ['adjustPageSize' => true]); 
                                }
                            } catch (\Exception $e) {
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
