<?php

namespace App\Filament\Resources\Letters\Schemas;

use App\Models\LetterTemplate;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class LetterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Registrasi & Klasifikasi')
                    ->extraAttributes([
                        'class' => implode(' ', [
                            '[&_.fi-section-header]:bg-gradient-to-br',
                            '[&_.fi-section-header]:from-emerald-500',
                            '[&_.fi-section-header]:to-teal-600',
                            '[&_.fi-section-header]:dark:from-emerald-900',
                            '[&_.fi-section-header]:dark:to-teal-950',
                            '[&_.fi-section-header]:rounded-t-2xl',
                            '[&_.fi-section-header-heading]:!text-white',
                            '[&_.fi-section-header-description]:!text-white/80',
                            '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                        ])
                    ])
                    ->schema([
                        Grid::make(1)->schema([
                            Select::make('classification')
                                ->label('Jenis Surat')
                                ->options([
                                    'Disposisi' => 'Disposisi',
                                    'Undangan' => 'Undangan',
                                    'Surat Dinas' => 'Surat Dinas',
                                    'Notulensi' => 'Notulensi',
                                ])
                                ->required()
                                ->default('Disposisi')
                                ->live()
                                ->native(false),

                            CheckboxList::make('urgency')
                                ->label('Sifat / Urgensi')
                                ->options([
                                    'Biasa' => 'Biasa',
                                    'Penting' => 'Penting',
                                    'Segera' => 'Segera',
                                    'Rahasia' => 'Rahasia',
                                ])
                                ->columns(2)
                                ->visible(fn (Get $get) => $get('classification') === 'Disposisi')
                                ->required(fn (Get $get) => $get('classification') === 'Disposisi'),

                            TextInput::make('agenda_number')
                                ->label('Nomor Agenda')
                                ->visible(fn (Get $get) => $get('classification') === 'Disposisi')
                                ->required(fn (Get $get) => $get('classification') === 'Disposisi'),

                            DatePicker::make('agenda_date')
                                ->label('Tanggal Agenda')
                                ->minDate(today())
                                ->required(),

                            TimePicker::make('time')
                                ->label('Waktu Pelaksanaan')
                                ->visible(fn (Get $get) => $get('classification') === 'Undangan')
                                ->required(fn (Get $get) => $get('classification') === 'Undangan'),
                                    
                            TextInput::make('location')
                                ->label('Lokasi Acara')
                                ->visible(fn (Get $get) => $get('classification') === 'Undangan')
                                ->required(fn (Get $get) => $get('classification') === 'Undangan'),

                            Select::make('template_id')
                                ->label('Pilih Template Surat')
                                ->options(LetterTemplate::pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->visible(fn (Get $get) => $get('classification') === 'Undangan')
                                ->required(fn (Get $get) => $get('classification') === 'Undangan')
                                ->dehydrated(false),

                            Textarea::make('note')
                                ->label('Keterangan Tambahan')
                                ->rows(fn (Get $get) => $get('classification') === 'Undangan' ? 1 : 2),
                        ]),
                    ])
                    ->columnSpan(1),

                Grid::make(2)->schema([
                    Section::make('Identitas Surat')
                        ->extraAttributes([
                            'class' => implode(' ', [
                                '[&_.fi-section-header]:bg-gradient-to-br',
                                '[&_.fi-section-header]:from-emerald-500',
                                '[&_.fi-section-header]:to-teal-600',
                                '[&_.fi-section-header]:dark:from-emerald-900',
                                '[&_.fi-section-header]:dark:to-teal-950',
                                '[&_.fi-section-header]:rounded-t-2xl',
                                '[&_.fi-section-header-heading]:!text-white',
                                '[&_.fi-section-header-description]:!text-white/80',
                                '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                            ])
                        ])
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('reference_number')
                                    ->label('Nomor Surat')
                                    ->required(),

                                TextInput::make('sender')
                                    ->label('Asal Surat / Pengirim')
                                    ->required(),

                                DatePicker::make('letter_date')
                                    ->label('Tanggal Surat')
                                    ->maxDate(today())
                                    ->required(),

                                Textarea::make('title')
                                ->label('Perihal / Acara')
                                ->rows(fn (Get $get) => $get('classification') === 'Undangan' ? 5 : 3)
                                ->required(),
                            ]),
                        ])
                        ->columnSpan(2),

                    Section::make('Instruksi Atasan')
                        ->extraAttributes([
                            'class' => implode(' ', [
                                '[&_.fi-section-header]:bg-gradient-to-br',
                                '[&_.fi-section-header]:from-emerald-500',
                                '[&_.fi-section-header]:to-teal-600',
                                '[&_.fi-section-header]:dark:from-emerald-900',
                                '[&_.fi-section-header]:dark:to-teal-950',
                                '[&_.fi-section-header]:rounded-t-2xl',
                                '[&_.fi-section-header-heading]:!text-white',
                                '[&_.fi-section-header-description]:!text-white/80',
                                '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                            ])
                        ])
                        ->visible(fn (Get $get) => $get('classification') === 'Disposisi')
                        ->schema([
                            Textarea::make('instruction')
                                ->label('Isi Instruksi / Catatan Disposisi')
                                ->rows(4)
                                ->required(fn (Get $get) => $get('classification') === 'Disposisi')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Dokumen & Keterangan')
                        ->extraAttributes([
                            'class' => implode(' ', [
                                '[&_.fi-section-header]:bg-gradient-to-br',
                                '[&_.fi-section-header]:from-emerald-500',
                                '[&_.fi-section-header]:to-teal-600',
                                '[&_.fi-section-header]:dark:from-emerald-900',
                                '[&_.fi-section-header]:dark:to-teal-950',
                                '[&_.fi-section-header]:rounded-t-2xl',
                                '[&_.fi-section-header-heading]:!text-white',
                                '[&_.fi-section-header-description]:!text-white/80',
                                '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                            ])
                        ])
                        ->schema([
                            Group::make([
                                FileUpload::make('file_path')
                                    ->label('Lampiran Berkas (PDF)')
                                    ->disk('public')
                                    ->directory('surat')
                                    ->required()
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(2048)
                                    ->helperText('Maksimal ukuran file 2MB')
                                    ->columnSpanFull(),
                            ])
                            ->extraAttributes(fn (Get $get) => $get('classification') === 'Undangan' ? [
                                'class' => '[&_.filepond--root]:!h-[72px] [&_.filepond--panel-root]:!h-[72px]',
                            ] : []),
                        ])
                        ->columnSpan(fn (Get $get) => $get('classification') === 'Disposisi' ? 1 : 2),
                ])
                ->columnSpan(2),

                Section::make('Distribusi / Tujuan Surat')
                    ->description('Pilih staf yang akan menerima notifikasi dan akses ke surat ini.')
                    ->extraAttributes([
                        'class' => implode(' ', [
                            '[&_.fi-section-header]:bg-gradient-to-br',
                            '[&_.fi-section-header]:from-emerald-500',
                            '[&_.fi-section-header]:to-teal-600',
                            '[&_.fi-section-header]:dark:from-emerald-900',
                            '[&_.fi-section-header]:dark:to-teal-950',
                            '[&_.fi-section-header]:rounded-t-2xl',
                            '[&_.fi-section-header-heading]:!text-white',
                            '[&_.fi-section-header-description]:!text-white/80',
                            '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                        ])
                    ])
                    ->schema([
                        CheckboxList::make('targetStaffs')
                            ->hiddenLabel()
                            ->relationship('targetStaffs', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "{$record->name} — " . ($record->chair->name ?? 'Tanpa Jabatan') . " (" . ($record->unit->name ?? 'Tanpa Unit') . ")"
                            )
                            ->columns(1) 
                            ->gridDirection('row')
                            ->searchable() 
                            ->bulkToggleable()
                            ->required()
                            ->extraAttributes([
                                'class' => 'max-h-64 overflow-y-auto border border-gray-200 dark:border-white/10 rounded-2xl p-4 shadow-sm'
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
