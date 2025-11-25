<?php

namespace App\Filament\Pages;

use App\Models\Staff;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Panel;

class Signature extends Page
{
    public ?string $signer = '-';
    public ?string $chair = '-';
    public ?string $date = '-';

    protected static string|BackedEnum|null $navigationIcon = null; 
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.signature';
    protected static ?string $slug = 'sign';
    protected static ?string $title = 'Signature';

    public function mount(): void
    {
        $subject = request()->query('verified_by') ?? request()->query('known_by') ?? request()->query('approve_by') ?? request()->query('replace_by'); 
        $dateParams = request()->query('verified_at') ?? request()->query('known_at') ?? request()->query('approve_at') ?? request()->query('replace_at'); 

        if ($subject) {
            $staff = Staff::find($subject); 
            if ($staff) {
                $this->signer = $staff->name;
                $this->chair = $staff->chair->name;
            }
        }

        if ($dateParams) {
            try {
                $this->date = Carbon::parse($dateParams)
                    ->locale('id')
                    ->isoFormat('D MMMM Y'); 
            } catch (\Exception $e) {
                $this->date = $dateParams;
            }
        }
    }

    public function getLayout(): string
    {
        return 'filament.pages.regLayout';
    }

    public static function getWithoutRouteMiddleware(Panel $panel): string|array
    {
        return ['auth'];
    }
    
    protected function getLayoutData(): array
    {
       return [
           'pageTitle' => static::$title,
       ];
    }
}