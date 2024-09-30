<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\Pages;

use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePrinter extends CreateRecord
{
    protected static string $resource = PrinterResource::class;
}
