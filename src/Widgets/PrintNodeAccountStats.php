<?php

namespace Consignr\FilamentPrintNode\Widgets;

use Consignr\FilamentPrintNode\Api;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PrintNodeAccountStats extends BaseWidget
{
    public array $response;

    protected function getStats(): array
    {
         return [
            Stat::make('Computers', $this->response['numComputers']),
            Stat::make('Total Prints', $this->response['totalPrints']),
            Stat::make('Account Status', ucfirst($this->response['state'])),
        ];
    }
}
