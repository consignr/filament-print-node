<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\Pages;

use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewPrinter extends ViewRecord
{
    protected static string $resource = PrinterResource::class;

    public function getTitle(): string|Htmlable
    {
        return "{$this->record->name}";
    }

    public function getSubheading(): string|Htmlable|null
    {
       return 'View Printer'; 
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
