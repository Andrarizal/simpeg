<?php

namespace App\Livewire;

use App\Models\PerformancePeriod;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class PerformancePeriodManager extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;

    public ?array $data = [];
    public ?int $editingId = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->live()
                    ->disabledDates(fn () => $this->getOccupiedDates($this->editingId))
                    // Minimal tanggal 1 bulan saat ini
                    ->minDate(now()->startOfMonth()) 
                    // Auto-koreksi ke Tanggal 1
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            // Apapun tanggal yang dipilih user, paksa jadi tanggal 1
                            $set('start_date', Carbon::parse($state)->startOfMonth()->toDateString());
                        }
                    }),

                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->native(false)
                    ->live()
                    ->disabledDates(fn () => $this->getOccupiedDates($this->editingId))
                    ->displayFormat('d F Y')
                    // Baru aktif setelah start_date diisi
                    ->disabled(fn (Get $get) => blank($get('start_date')))
                    // Minimal harus setelah start_date
                    ->minDate(fn (Get $get) => $get('start_date') ? Carbon::parse($get('start_date')) : null)
                    // Maksimal akhir tahun dari start_date (Tahun yang sama)
                    ->maxDate(fn (Get $get) => $get('start_date') ? Carbon::parse($get('start_date'))->endOfYear() : null)
                    // Auto-koreksi ke Tanggal Terakhir Bulan Tersebut
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            // Apapun tanggal yang dipilih, paksa jadi End of Month
                            $set('end_date', Carbon::parse($state)->endOfMonth()->toDateString());
                        }
                    }),
                Toggle::make('status')
                    ->label('Aktif')
                    ->default(1),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->statePath('data')
            ->model(PerformancePeriod::class);
    }

    protected function getOccupiedDates(?int $ignoreId = null): array
    {
        $periods = PerformancePeriod::query()
            ->select('start_date', 'end_date')
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->get();
        
        $blockedDates = [];

        foreach ($periods as $period) {
            // Generate setiap tanggal dari start sampai end
            $range = CarbonPeriod::create($period->start_date, $period->end_date);
            
            foreach ($range as $date) {
                $blockedDates[] = $date->toDateString();
            }
        }

        return $blockedDates;
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['year'] = Carbon::parse($data['start_date'])->translatedFormat('Y');

        if ($this->editingId){
            $period = PerformancePeriod::find($this->editingId);
            $period->update($data);
            
            Notification::make()
                ->title('Periode berhasil diperbarui')
                ->success()
                ->send();
        } else {
            PerformancePeriod::create($data);
    
            Notification::make()
                ->title('Periode berhasil dibuat')
                ->success()
                ->send();
        }
        
        $this->cancelEdit();
    }

    public function editPeriod(PerformancePeriod $record): void
    {
        $this->editingId = $record->id;
        $this->form->fill($record->toArray());
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->form->fill(); 
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(PerformancePeriod::query()->latest())
            ->columns([
                TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('period_range') 
                    ->label('Periode Bulan')
                    ->state(function (PerformancePeriod $record) {
                        $start = Carbon::parse($record->start_date);
                        $end = Carbon::parse($record->end_date);

                        return $start->translatedFormat('F') . ' - ' . $end->translatedFormat('F');
                    }),
                ToggleColumn::make('status'),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    // Saat diklik, panggil fungsi editPeriod di PHP
                    ->action(fn (PerformancePeriod $record) => $this->editPeriod($record)),
                Action::make('delete')
                    ->label('Hapus')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->action(function (PerformancePeriod $record){
                        PerformancePeriod::find($record->id)->delete();

                        Notification::make()
                            ->title('Periode berhasil dihapus')
                            ->success()
                            ->send();
                    }),
            ])
            ->paginated(false);
    }

    public function render()
    {
        return view('livewire.performance-period-manager');
    }
}
