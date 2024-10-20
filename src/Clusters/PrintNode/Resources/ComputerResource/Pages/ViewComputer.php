<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\ComputerResource\Pages;

use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\ComputerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewComputer extends ViewRecord
{
    protected static string $resource = ComputerResource::class;

    public function getTitle(): string|Htmlable
    {
        return "{$this->record->name}";
    }

    public function getSubheading(): string|Htmlable|null
    {
        $label = ComputerResource::getLabel();
       return "View {$label}"; 
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
