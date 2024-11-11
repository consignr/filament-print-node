<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Consignr\FilamentPrintNode\Models\Computer;
use Consignr\FilamentPrintNode\Clusters\PrintNode;
use Consignr\FilamentPrintNode\Models\Client as ClientModel;
use Consignr\FilamentPrintNode\Widgets\PrintNodeClientDownloads;

class Client extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-down';

    protected static ?string $navigationLabel = 'Get Client';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament-print-node::pages.client';

    protected static ?string $cluster = PrintNode::class;

    public static function getNavigationBadge(): ?string
    {
        return static::hasComputersWithUpgradableClients() ? 'New version' : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        if (static::hasComputersWithUpgradableClients()) {

            $version = static::getLatestVersion();

            return "Version {$version} is now available.";
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PrintNodeClientDownloads::class,
        ];
    }

    protected static function getLatestVersion()
    {
        return collect(app(ClientModel::class)->getRows())->pluck('version')->first();
    }

    protected static function getComputerVersions(): Collection
    {
        return Computer::pluck('version', 'name');
    }

    protected static function hasComputersWithUpgradableClients(): bool
    {
        return static::computersWithUpgradableClients()->count() > 0;
    }

    protected static function computersWithUpgradableClients(): Collection
    {
        return static::getComputerVersions()->reject(fn ($value) => $value === static::getLatestVersion());
    }
}
