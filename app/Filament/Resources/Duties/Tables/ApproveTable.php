<?php

namespace App\Filament\Resources\Duties\Tables;

use App\Filament\Resources\Duties\DutyResource;
use App\Models\Duty;
use App\Models\Staff;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ApproveTable
{
    public static function configure(Table $table, ?Staff $staff): Table
    {
        return $table
            ->query(function () use ($staff) {
                return Duty::query()->whereHas('targetStaffs', function (Builder $query) use ($staff) {
                    $query->where('staff_id', $staff->id);
                })->with(['targetStaffs' => function ($query) use ($staff) {
                    $query->where('staff_id', $staff->id);
                }]);
            })
            ->columns([
                TextColumn::make('duty_date')
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->alignCenter(),
                TextColumn::make('time')
                    ->label('Waktu Penugasan')
                    ->state(function ($record) {
                        $start = Carbon::parse($record->start_time)->translatedFormat('H:i');
                        $end = $record->end_time ? Carbon::parse($record->end_time)->translatedFormat('H:i') : 'selesai';
                        return "$start - $end";
                    }),
                IconColumn::make('is_workhour')
                    ->label('di Jam Kerja?')
                    ->alignCenter()
                    ->default('null')
                    ->getStateUsing(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return $pivot?->is_workhour;
                    })
                    ->icon(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return match ($pivot?->is_workhour) {
                            1 => 'heroicon-o-check-circle',
                            0 => 'heroicon-o-x-circle',
                            default => 'heroicon-o-clock',
                        };
                    })
                    ->color(function ($record) {
                      $pivot = $record->targetStaffs->first()?->pivot;
                        return match ($pivot?->is_workhour) {
                            1 => 'warning',
                            0 => 'success',
                            default => 'gray',
                        };
                    })
                    ->tooltip(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return match ($pivot?->is_workhour) {
                            1 => 'di Jam Kerja',
                            0 => 'Tidak di Jam Kerja',
                            default => 'Belum Melapor',
                        };
                    }),
                TextColumn::make('duty')
                    ->label('Acara')
                    ->searchable()
                    ->wrap()
                    ->formatStateUsing(fn (string $state): string => substr(nl2br(e($state)), 0, 100) . (strlen(nl2br(e($state))) > 100 ? '...' : ''))
                    ->html()
                    ->extraAttributes([
                        'class' => 'min-w-xs', 
                    ]),
                TextColumn::make('image_verified')
                    ->label('Verifikasi Foto')
                    ->alignCenter()
                    ->badge()
                    ->default('null')
                    ->getStateUsing(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return $pivot?->image_path ? 'Sudah Upload' : 'Belum Upload';
                    })
                    ->icon(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return match ($pivot?->image_verified) {
                            1 => 'heroicon-m-check-circle',
                            0 => 'heroicon-m-x-circle',
                            default => 'heroicon-m-clock',
                        };
                    })
                    ->color(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        if ($pivot?->image_path && is_null($pivot->image_verified)) return 'warning';

                        return match ($pivot?->image_verified) {
                            1 => 'info',
                            0 => 'danger',
                            default => 'gray',
                        };
                    })
                    ->tooltip(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return match ($pivot?->image_verified) {
                            1 => 'Diverifikasi',
                            0 => 'Ditolak',
                            default => 'Belum direspon',
                        };
                    })
                    ->action(
                        Action::make('preview_image')
                            ->modalHeading('Preview Foto di Lokasi')
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->modalContent(function ($record) {
                                $path = $record->targetStaffs->first()?->pivot?->image_path;
                                $url = route('file.preview', ['path' => $path]);
                                
                                return new HtmlString('<img src="'.$url.'" class="w-full h-auto rounded-xl shadow-md" alt="Foto Selfie">');
                            })
                            ->disabled(fn ($record) => empty($record->targetStaffs->first()?->pivot?->image_path)),
                    ),

                TextColumn::make('content_verified')
                    ->label('Verifikasi Materi')
                    ->alignCenter()
                    ->badge()
                    ->default('null')
                    ->getStateUsing(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return $pivot?->content_path ? 'Sudah Upload' : 'Belum Upload';
                    })
                    ->icon(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return match ($pivot?->content_verified) {
                            1 => 'heroicon-m-check-circle',
                            0 => 'heroicon-m-x-circle',
                            default => 'heroicon-m-clock',
                        };
                    })
                    ->color(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        if ($pivot?->content_path && is_null($pivot->content_verified)) return 'warning';

                        return match ($pivot?->content_verified) {
                            1 => 'info',
                            0 => 'danger',
                            default => 'gray',
                        };
                    })
                    ->tooltip(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return match ($pivot?->content_verified) {
                            1 => 'Diverifikasi',
                            0 => 'Ditolak',
                            default => 'Belum direspon',
                        };
                    })
                    ->action(
                        Action::make('preview_content')
                            ->modalHeading('Preview Materi (PDF/PPT)')
                            ->modalWidth('4xl') 
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->modalContent(function ($record) {
                                $path = $record->targetStaffs->first()?->pivot?->content_path;
                                
                                $url = route('file.preview', ['path' => $path]);
                                
                                return view('filament.components.preview-pdf-2', [
                                  'url' => $url
                                ]);
                            })
                            ->disabled(fn ($record) => empty($record->targetStaffs->first()?->pivot?->content_path)),
                    ),

                TextColumn::make('letter_verified')
                    ->label('Verifikasi Surat')
                    ->alignCenter()
                    ->badge()
                    ->default('null')
                    ->getStateUsing(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return $pivot?->letter_path ? 'Sudah Upload' : 'Belum Upload';
                    })
                    ->icon(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return match ($pivot?->letter_verified) {
                            1 => 'heroicon-m-check-circle',
                            0 => 'heroicon-m-x-circle',
                            default => 'heroicon-m-clock',
                        };
                    })
                    ->color(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        if ($pivot?->letter_path && is_null($pivot->letter_verified)) return 'warning';

                        return match ($pivot?->letter_verified) {
                            1 => 'info',
                            0 => 'danger',
                            default => 'gray',
                        };
                    })
                    ->tooltip(function ($record) {
                        $pivot = $record->targetStaffs->first()?->pivot;
                        return match ($pivot?->letter_verified) {
                            1 => 'Diverifikasi',
                            0 => 'Ditolak',
                            default => 'Belum direspon',
                        };
                    })
                    ->action(
                        Action::make('preview_letter')
                            ->modalHeading('Preview Surat Tugas Berstempel')
                            ->modalWidth('4xl')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->modalContent(function ($record) {
                                $path = $record->targetStaffs->first()?->pivot?->letter_path;
                                $url = route('file.preview', ['path' => $path]);
                                
                                if (preg_match('/\.(jpg|jpeg|png)$/i', $path)) {
                                    return new HtmlString('<img src="'.$url.'" class="w-full h-auto rounded-xl shadow-md" alt="Surat Tugas">');
                                }
                                
                                return view('filament.components.preview-pdf-2', [
                                  'url' => $url
                                ]);
                            })
                            ->disabled(fn ($record) => empty($record->targetStaffs->first()?->pivot?->letter_path)),
                    ),
            ])
            ->filters([
                SelectFilter::make('month_year')
                    ->label('Bulan')
                    ->options(function () {
                        return Duty::query()
                            ->select('duty_date')
                            ->whereNotNull('duty_date')
                            ->orderBy('duty_date', 'desc') 
                            ->get()
                            ->map(function ($item) {
                                return Carbon::parse($item->duty_date)->format('Y-m');
                            })
                            ->unique() 
                            ->mapWithKeys(function ($dateString) {
                                return [
                                    $dateString => Carbon::createFromFormat('Y-m', $dateString)->translatedFormat('F Y')
                                ];
                            })
                            ->toArray();
                    })
                    ->default(function () {
                        $latestLetter = Duty::whereNotNull('created_at')
                            ->orderBy('created_at', 'desc')
                            ->first();

                        return $latestLetter 
                            ? Carbon::parse($latestLetter->letter_date)->format('Y-m') 
                            : now()->format('Y-m');
                    })
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return;

                        $date = Carbon::createFromFormat('Y-m', $data['value']);

                        $query->whereMonth('duty_date', $date->month)
                            ->whereYear('duty_date', $date->year);
                    })
                    ->indicateUsing(function (array $data) {
                        if (empty($data['value'])) return [];

                        return [
                            Indicator::make('Bulan: ' . Carbon::parse($data['value'])->translatedFormat('F Y'))
                            ->removable(false),
                        ];
                    })
                    ->selectablePlaceholder(false)
                    ->native(false),
            ])
            ->recordActions([
                Action::make('verification')
                  ->label('Respon')
                  ->icon('heroicon-o-check')
                  ->color('info')
                  ->visible(function ($record) { 
                      $pivot = $record->targetStaffs->first()?->pivot;

                      return (is_null($pivot->image_verified) || 
                      is_null($pivot->content_verified) || 
                      is_null($pivot->letter_verified)) &&
                      Auth::user()->role_id == 1;
                    })
                  ->modalHeading('Detail Verifikasi')
                  ->modalDescription('Lengkapi verifikasi dokumen untuk tugas ini.')
                  ->modalAlignment(Alignment::Center)
                  ->modalWidth('md')
                  ->modalSubmitActionLabel('Verifikasi')
                  ->requiresConfirmation()
                  ->fillForm(function ($record) {
                      $pivot = $record->targetStaffs->first()?->pivot;

                      return [
                          'image_verified'   => $pivot?->image_verified,
                          'content_verified' => $pivot?->content_verified,
                          'letter_verified'  => $pivot?->letter_verified,
                      ];
                  })
                  ->schema([
                      Grid::make(1)->schema([
                          ToggleButtons::make('image_verified')
                              ->label('Verifikasi Foto')
                              ->options([
                                  1 => 'Verifikasi',
                                  0 => 'Tolak',
                              ])
                              ->colors([
                                  1 => 'info',
                                  0 => 'danger',
                              ])
                              ->inline()
                              ->inlineLabel()
                              ->required()
                              ->extraAttributes([
                                  'class' => 'flex justify-end w-full [&_div[role="group"]]:justify-end',
                              ]),
                          ToggleButtons::make('content_verified')
                              ->label('Verifikasi Materi')
                              ->options([
                                  1 => 'Verifikasi',
                                  0 => 'Tolak',
                              ])
                              ->colors([
                                  1 => 'info',
                                  0 => 'danger',
                              ])
                              ->inline()
                              ->inlineLabel()
                              ->required()
                              ->extraAttributes([
                                  'class' => 'flex justify-end w-full [&_div[role="group"]]:justify-end',
                              ]),
                          ToggleButtons::make('letter_verified')
                              ->label('Verifikasi Surat')
                              ->options([
                                  1 => 'Verifikasi',
                                  0 => 'Tolak',
                              ])
                              ->colors([
                                  1 => 'info',
                                  0 => 'danger',
                              ])
                              ->inline()
                              ->inlineLabel()
                              ->required()
                              ->extraAttributes([
                                  'class' => 'flex justify-end w-full [&_div[role="group"]]:justify-end',
                              ]),
                      ])
                  ])
                  ->action(function (array $data, $record) use ($staff) {
                      $record->targetStaffs()
                          ->where('staff_id', $staff->id)
                          ->update([
                              'image_verified' => $data['image_verified'],
                              'content_verified' => $data['content_verified'],
                              'letter_verified' => $data['letter_verified'],
                          ]);

                      Notification::make()
                          ->title('Tugas Diverifikasi')
                          ->body('Tugas Anda untuk ' . Carbon::parse($record->duty_date)->translatedFormat('d F Y') . ' telah diverifikasi SDM')
                          ->success()
                          ->actions([
                              Action::make('read')
                                  ->button()
                                  ->url(DutyResource::getUrl('index'))
                                  ->markAsRead()
                          ])
                          ->sendToDatabase($staff->user);

                      Notification::make()
                          ->title('Tugas diverifikasi')
                          ->success()
                          ->send();
                  }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
