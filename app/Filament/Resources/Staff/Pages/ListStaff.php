<?php

namespace App\Filament\Resources\Staff\Pages;

use App\Filament\Resources\Staff\StaffResource;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class ListStaff extends ListRecords
{
    use WithFileUploads;
    
    protected static string $resource = StaffResource::class;

    protected static ?string $title = 'Daftar Pegawai';

    public $file;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
            ->label('Impor Pegawai')
            ->icon('heroicon-o-arrow-up-tray')
            ->modalHeading('Impor Data Pegawai')
            ->modalDescription('Unggah file Excel atau CSV berisi data pegawai. Sistem akan memperbarui data yang sudah ada (berdasarkan NIK).')
            ->form([
                FileUpload::make('file')
                    ->label('File Excel / CSV')
                    ->required()
                    ->acceptedFileTypes([
                        'text/csv',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->disk('public') 
                    ->directory('imports'),
                Action::make('download_template')
                    ->label('Unduh Template')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(asset('templates/staff_import_template.xlsx'))
                    ->color('gray'),
            ])
            ->action(function (array $data) {
                try {
                    $fullPath = storage_path('app/public/' . $data['file']);
                    $rows = Excel::toCollection(null, $fullPath)->first();
                    
                    $headers = $rows->shift(); // Ambil baris pertama (header)
                    $rows = $rows->map(fn ($row) => $headers->combine($row)); // Gabungkan header dengan data
                    
                    DB::transaction(function () use ($rows) {
                        foreach ($rows as $row) {
                            if (empty($row['nik']) || empty($row['name'])) continue;

                            Staff::updateOrCreate(
                            ['nik' => $row['nik']],
                            [
                                'name' => $row['name'],
                                'birth_place' => $row['birth_place'] ?? null,
                                'birth_date' => isset($row['birth_date']) ? \Carbon\Carbon::parse($row['birth_date']) : null,
                                'sex' => $row['sex'] ?? 'L',
                                'address' => $row['address'] ?? null,
                                'phone' => $row['phone'] ?? null,
                                'personal_email' => $row['personal_email'] ?? null,
                                'office_email' => $row['office_email'] ?? null,
                                'last_education' => $row['last_education'] ?? null,
                                'work_entry_date' => isset($row['work_entry_date']) ? \Carbon\Carbon::parse($row['work_entry_date']) : null,
                                'unit_id' => $row['unit_id'] ?? null,
                                'chair_id' => $row['chair_id'] ?? null,
                                ]
                            );
                        }
                    });
                } finally {
                    $relativePath = str_replace(storage_path('app/public/'), '', $fullPath);

                    if (Storage::disk('public')->exists($relativePath)) Storage::disk('public')->delete($relativePath);
                }
                
                Notification::make()
                ->title('Data pegawai berhasil diimpor!')
                ->success()
                ->send();
            }),
            CreateAction::make(),
        ];
    }

    protected function getBreadcrumbTitle(): string
    {
        return 'Daftar Pegawai';
    }
}
