<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\ComputerResource\Pages;

use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\ComputerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComputers extends ListRecords
{
    protected static string $resource = ComputerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
