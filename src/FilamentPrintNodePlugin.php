<?php

namespace Consignr\FilamentPrintNode;

use Filament\Panel;
use Filament\Contracts\Plugin;

class FilamentPrintNodePlugin implements Plugin
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
                //
            ])
            ->pages([
                //
            ])
            ->discoverClusters(in: __DIR__.'\Clusters', for: 'Consignr\\FilamentPrintNode\\Clusters');
    }
 
    public function boot(Panel $panel): void
    {
        //
    }
}