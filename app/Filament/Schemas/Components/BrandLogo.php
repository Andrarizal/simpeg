<?php

namespace App\Filament\Schemas\Components;

use Filament\Schemas\Components\Component;

class BrandLogo extends Component
{
    protected string $view = 'filament.schemas.components.brand-logo';

    public static function make(): static
    {
        return app(static::class);
    }
}
