<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource\Pages;

use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrintJobs extends ListRecords
{
    protected static string $resource = PrintJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
