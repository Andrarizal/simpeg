<?php

namespace App\Filament\Resources\PreStaff;

use App\Filament\Resources\PreStaff\Pages\ManagePreStaff;
use App\Mail\SendPreStaffVerification;
use App\Models\Chair;
use App\Models\Group;
use App\Models\PreStaff;
use App\Models\StaffStatus;
use App\Models\Unit;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PreStaffResource extends Resource
{
    protected static ?string $model = PreStaff::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserPlus;

    protected static ?string $recordTitleAttribute = 'PreStaff';

    protected static ?string $modelLabel = 'Pegawai Baru';       
    protected static ?string $pluralModelLabel = 'Pegawai Baru'; 
    protected static ?string $navigationLabel = 'Pegawai Baru';

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('PreStaff')
            ->columns([
                TextColumn::make('nik')
                    ->label('NIK')
                    ->sortable(),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('No Telepon')
                    ->searchable(),
                IconColumn::make('status')
                    ->label('Terverifikasi')
                    ->alignCenter()
                    ->icon(fn ($state) => match ($state) {
                        'Diverifikasi' => 'heroicon-o-check-circle',
                        'Ditolak' => 'heroicon-o-x-circle',
                        'Menunggu' => 'heroicon-o-clock',
                    })
                    ->color(fn ($state) => match ($state) {
                        'Diverifikasi' => 'info',
                        'Ditolak' => 'danger',
                        'Menunggu' => 'warning',
                    })
                    ->tooltip(fn ($state) => match ($state) {
                        'Diverifikasi' => 'Diverifikasi',
                        'Ditolak' => 'Ditolak',
                        'Menunggu' => 'Belum direspon',
                    }),
                TextColumn::make('token')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-m-paper-airplane') // Icon pesawat kertas
                    ->color('info') 
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Email Verifikasi?')
                    ->modalWidth('xl')
                    ->modalDescription('Isi data pegawai berikut untuk verifikasi. Verifikasi berarti calon user akan menerima email berisi link token untuk registrasi ulang.')
                    ->modalSubmitActionLabel('Ya, Kirim')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('staff_status_id')
                                    ->label('Status Kepegawaian')
                                    ->options(StaffStatus::pluck('name', 'id'))
                                    ->reactive()
                                    ->required()
                                    ->native(false),
                                Select::make('chair_id')
                                    ->label('Jabatan')
                                    ->options(Chair::pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false),
                                Select::make('group_id')
                                    ->label('Kelompok Tenaga Kerja')
                                    ->options(Group::pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false),
                                Select::make('unit_id')
                                    ->label('Unit Kerja')
                                    ->options(Unit::pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false),
                            ])
                            ->columns(2)
                        ])
                    ->action(function (PreStaff $record, array $data) {
                        try {
                            Mail::to($record->email)
                                ->queue(new SendPreStaffVerification($record));

                            $record->update([
                                'status' => 'Diverifikasi',
                                'staff_status_id' => $data['staff_status_id'],
                                'chair_id' => $data['chair_id'],
                                'group_id' => $data['group_id'],
                                'unit_id' => $data['unit_id'],
                            ]);

                            Notification::make()
                                ->title('Email verifikasi berhasil dikirim ke ' . $record->email)
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal mengirim email')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePreStaff::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->role_id == 1;
    }
}
