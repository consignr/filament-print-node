<?php
 
namespace Consignr\FilamentPrintNode;
 
use Filament\Panel;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
 
class FilamentPrintNodeProvider extends PackageServiceProvider
{
    public static string $name = 'filament-print-node';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
            

        if (file_exists($package->basePath('/../config/'.static::$name.'.php'))) {
            $package->hasConfigFile();
        }
    }
}