<?php

namespace App\Livewire;

use App\Models\MonthlyPeriod;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class MonthlyPeriodManager extends Component implements HasForms, HasTable, HasActions
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
            Grid::make(2)
                ->schema([
                    DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->required()
                        ->native(false)
                        ->displayFormat('d F Y')
                        ->live()
                        ->disabledDates(fn () => $this->getOccupiedDates($this->editingId)),

                    DatePicker::make('end_date')
                        ->label('Tanggal Selesai')
                        ->required()
                        ->native(false)
                        ->live()
                        ->disabledDates(fn () => $this->getOccupiedDates($this->editingId))
                        ->displayFormat('d F Y')
                        ->disabled(fn (Get $get) => blank($get('start_date')))
                        ->minDate(fn (Get $get) => $get('start_date') ? Carbon::parse($get('start_date'))->addDays(1) : null),
                ]),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->statePath('data')
            ->model(MonthlyPeriod::class);
    }

    protected function getOccupiedDates(?int $ignoreId = null): array
    {
        $periods = MonthlyPeriod::query()
            ->select('start_date', 'end_date')
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->get();
        
        $blockedDates = [];

        foreach ($periods as $period) {
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
        $start = Carbon::parse($data['start_date'])->translatedFormat('M');
        $end = Carbon::parse($data['end_date'])->translatedFormat('M');

        if (Carbon::parse($data['start_date'])->translatedFormat('Y') === Carbon::parse($data['end_date'])->translatedFormat('Y')) {
            $year = Carbon::parse($data['end_date'])->translatedFormat('Y');
            $data['name'] = $start . ' - ' . $end . ' ' . $year;
        } else {
            $yearStart = Carbon::parse($data['start_date'])->translatedFormat('Y');
            $yearEnd = Carbon::parse($data['end_date'])->translatedFormat('Y');
            $data['name'] = $start . ' ' . $yearStart . ' - ' . $end . ' ' . $yearEnd;
        }

        if ($this->editingId){
            $period = MonthlyPeriod::find($this->editingId);
            $period->update($data);
            
            Notification::make()
                ->title('Periode berhasil diperbarui')
                ->success()
                ->send();
        } else {
            MonthlyPeriod::create($data);
    
            Notification::make()
                ->title('Periode berhasil dibuat')
                ->success()
                ->send();
        }
        
        $this->cancelEdit();
    }

    public function editPeriod(MonthlyPeriod $record): void
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
            ->query(MonthlyPeriod::query()->latest())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Periode'),
                TextColumn::make('period_range') 
                    ->label('Periode Bulan')
                    ->state(function (MonthlyPeriod $record) {
                        $start = Carbon::parse($record->start_date);
                        $end = Carbon::parse($record->end_date);

                        return $start->translatedFormat('d F') . ' - ' . $end->translatedFormat('d F');
                    }),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    ->action(fn (MonthlyPeriod $record) => $this->editPeriod($record)),
                Action::make('delete')
                    ->label('Hapus')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->action(function (MonthlyPeriod $record){
                        MonthlyPeriod::find($record->id)->delete();

                        Notification::make()
                            ->title('Periode berhasil dihapus')
                            ->success()
                            ->send();
                    }),
            ])
            ->paginated();
    }

    public function render()
    {
        return view('livewire.period-manager');
    }
}
