<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\Pages;

use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrinters extends ListRecords
{
    protected static string $resource = PrinterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
