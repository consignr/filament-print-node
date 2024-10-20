<?php

namespace Consignr\FilamentPrintNode\Clusters;

use Consignr\FilamentPrintNode\FilamentPrintNodePlugin;
use Filament\Clusters\Cluster;

class PrintNode extends Cluster
{
    public static function getNavigationGroup(): ?string
    {
        return FilamentPrintNodePlugin::get()->getNavigationGroup();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentPrintNodePlugin::get()->getNavigationLabel();
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentPrintNodePlugin::get()->getNavigationIcon();
    }
    
    public static function getNavigationSort(): ?int
    {
        return FilamentPrintNodePlugin::get()->getNavigationSort();
    }
}
