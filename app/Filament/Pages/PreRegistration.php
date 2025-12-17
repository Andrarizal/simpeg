<?php

namespace App\Filament\Pages;

use App\Models\PreStaff;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Http\Middleware\Authenticate;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Contracts\HasSchemas; 
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PreRegistration extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    public array $data = [];

    protected static string|BackedEnum|null $navigationIcon = null; 
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.pre-registration';
    protected static ?string $slug = 'pre-regist';
    protected static ?string $title = 'PreRegistration | SIMANTAP';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nik')
                    ->label('NIK')
                    ->placeholder('ex. 3321029920192099')
                    ->maxLength(16)
                    ->numeric()
                    ->unique('staff', 'nik')
                    ->unique('pre_staff', 'nik')
                    ->required(),
                TextInput::make('nip')
                    ->label('NIP')
                    ->mask('9999.9999.999.9')
                    ->maxLength(16)
                    ->inputMode('numeric')
                    ->placeholder('ex. 3321.0299.201.9')
                    ->required(),
                TextInput::make('name')
                    ->label('Nama')
                    ->placeholder('ex. Tamam Muhammad')
                    ->required(),
                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->required()
                    ->native(false),
                TextInput::make('email')
                    ->label('Email Pribadi')
                    ->email()
                    ->unique('staff', 'email')
                    ->unique('pre_staff', 'email')
                    ->placeholder('ex. tamam@gmail.com')
                    ->required(),
                TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel()
                    ->mask('9999-9999-9999')
                    ->placeholder('ex. 0812-3456-7890')
                    ->required(),
            ])
            ->columns(2)
            ->statePath('data'); 
    }

    public function preRegist() {
        $data = $this->form->validate(); 
        $validated = $data['data'];

        PreStaff::create([
            'nik' => $validated['nik'],
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'birth_date' => $validated['birth_date'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'token' => Str::uuid(),
            'status' => 'Menunggu',
        ]);

        Notification::make()
            ->title('Data telah terkirim dan sedang diverifikasi. Silahkan cek email secara berkala untuk registrasi ulang!')
            ->success()
            ->send();

        $this->form->fill();
    }

    public function getLayout(): string
    {
        return 'filament.pages.layout';
    }

    public static function getWithoutRouteMiddleware(Panel $panel): string|array
    {
        return [Authenticate::class];
    }
    
    protected function getLayoutData(): array
    {
       return [
           'pageTitle' => static::$title,
       ];
    }
}