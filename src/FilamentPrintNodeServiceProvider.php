<?php
 
namespace Consignr\FilamentPrintNode;
 
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
 
class FilamentPrintNodeServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-print-node';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);            

        if (file_exists($package->basePath('/../config/'.static::$name.'.php'))) {
            $package->hasConfigFile()
                ->hasViews();
        }        
    }

    public function packageBooted(): void
    {
        Livewire::component('print-node-account-stats-widget', Widgets\PrintNodeAccountStats::class);
        Livewire::component('print-node-account-overview-widget', Widgets\PrintNodeAccountOverview::class);
        Livewire::component('print-node-client-downloads-widget', Widgets\PrintNodeClientDownloads::class);
    }
}