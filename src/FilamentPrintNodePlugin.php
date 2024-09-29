<?php

namespace Consignr\FilamentPrintNode;

use Filament\Panel;
use Filament\Contracts\Plugin;

class FilamentPrintNodePlugin extends Plugin
{
    public function getId(): string
    {
        return 'filament-print-node';
    }

    public static function make(): static
    {
        return app(static::class);
    }
 
    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                // PostResource::class,
                // CategoryResource::class,
            ])
            ->pages([
                // Settings::class,
            ]);
    }
 
    public function boot(Panel $panel): void
    {
        //
    }
}