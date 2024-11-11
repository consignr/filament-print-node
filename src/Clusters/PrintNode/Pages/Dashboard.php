<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Pages;

use Filament\Pages\Page;
use Consignr\FilamentPrintNode\Clusters\PrintNode;
use Consignr\FilamentPrintNode\Widgets;
use Consignr\FilamentPrintNode\Api;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string $view = 'filament-print-node::pages.dashboard';

    protected static ?string $cluster = PrintNode::class;

    protected function getHeaderWidgets(): array
    {
        $printNode = new Api\PrintNode(config('filament-print-node.api_key'));

        $response = $printNode->send(new Api\Requests\GetWhoAmI);

        return [
            Widgets\PrintNodeAccountStats::make(['response' => $response->json()]),
            Widgets\PrintNodeAccountOverview::make(['response' => $response->json()]),
        ];
    }
}
