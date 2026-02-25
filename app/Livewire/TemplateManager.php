<?php

namespace App\Livewire;

use App\Models\LetterTemplate;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;

class TemplateManager extends Component implements HasForms, HasTable, HasActions
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
                    TextInput::make('name')
                        ->label('Nama Template')
                        ->required(),
                    TextInput::make('opening_greeting')
                        ->label('Salam Pembuka')
                        ->required(),
                    Textarea::make('opening_body')
                        ->label('Isi Pembuka')
                        ->rows(3)
                        ->required(),
                    Textarea::make('closing_body')
                        ->label('Isi Penutup')
                        ->rows(3)
                        ->required(),
                ]),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->statePath('data')
            ->model(LetterTemplate::class);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if ($this->editingId){
            $period = LetterTemplate::find($this->editingId);
            $period->update($data);
            
            Notification::make()
                ->title('Template berhasil diperbarui')
                ->success()
                ->send();
        } else {
            LetterTemplate::create($data);
    
            Notification::make()
                ->title('Template berhasil dibuat')
                ->success()
                ->send();
        }
        
        $this->cancelEdit();
    }

    public function editTemplate(LetterTemplate $record): void
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
            ->query(LetterTemplate::query()->latest())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Template'),
                TextColumn::make('content') 
                    ->label('Isi Template')
                    ->state(function (LetterTemplate $record) {
                        $fullHtml = "{$record->opening_greeting} <br> {$record->opening_body} <br> {$record->closing_body}";
                        
                        $previewHtml = "{$record->opening_greeting} <br> " . Str::limit($record->opening_body, 25);

                        $html = '
                            <div x-data="{ expanded: false }">
                                <div x-show="!expanded">
                                    ' . $previewHtml . '
                                    <button type="button" @click="expanded = true" class="text-primary-600 dark:text-primary-400 text-sm font-medium hover:underline ml-1">
                                        lihat selengkapnya
                                    </button>
                                </div>
                                
                                <div x-show="expanded" style="display: none;">
                                    ' . $fullHtml . '
                                    <button type="button" @click="expanded = false" class="text-primary-600 dark:text-primary-400 text-sm font-medium hover:underline block mt-1">
                                        sembunyikan
                                    </button>
                                </div>
                            </div>
                        ';

                        return new HtmlString($html);
                    })
                    ->wrap()
                    ->extraAttributes(['class' => 'min-w-xs']),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    ->action(fn (LetterTemplate $record) => $this->editTemplate($record)),
                Action::make('delete')
                    ->label('Hapus')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->action(function (LetterTemplate $record){
                        LetterTemplate::find($record->id)->delete();

                        Notification::make()
                            ->title('Template berhasil dihapus')
                            ->success()
                            ->send();
                    }),
            ])
            ->paginated();
    }

    public function render()
    {
        return view('livewire.template-manager');
    }
}
