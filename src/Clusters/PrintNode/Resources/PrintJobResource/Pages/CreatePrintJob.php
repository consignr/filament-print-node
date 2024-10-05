<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource\Pages;

use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePrintJob extends CreateRecord
{
    protected static string $resource = PrintJobResource::class;
}
