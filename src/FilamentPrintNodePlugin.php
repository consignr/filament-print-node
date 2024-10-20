<?php

namespace Consignr\FilamentPrintNode;

use Closure;
use Filament\Panel;
use Filament\Contracts\Plugin;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentPrintNodePlugin implements Plugin
{
    use EvaluatesClosures;

    protected string | Closure | null $navigationGroup = null;

    protected string | Closure | null $navigationLabel = null;

    protected string | Closure | null $navigationIcon = null;

    protected int | Closure | null $navigationSort = null;

    protected string | Closure | null $computerLabel = null;

    protected string | Closure | null $computerPluralLabel = null;

    protected string | Closure | null $computerNavigationLabel = null;

    protected string | Closure | null $computerNavigationIcon = null;

    protected int | Closure | null $computerNavigationSort = null;

    protected int | Closure | null $computerNavigationBadgeCount = null;

    protected string | Closure | null $printersLabel = null;

    protected string | Closure | null $printersPluralLabel = null;

    protected string | Closure | null $printersNavigationLabel = null;

    protected string | Closure | null $printersNavigationIcon = null;

    protected int | Closure | null $printersNavigationSort = null;

    protected int | Closure | null $printersNavigationBadgeCount = null;

    protected string | Closure | null $printJobLabel = null;

    protected string | Closure | null $printJobPluralLabel = null;

    protected string | Closure | null $printJobNavigationLabel = null;

    protected string | Closure | null $printJobNavigationIcon = null;

    protected int | Closure | null $printJobNavigationSort = null;

    protected int | Closure | null $printJobNavigationBadgeCount = null;
    
    public function getId(): string
    {
        return 'filament-print-node';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): Plugin
    {
        return filament(app(static::class)->getId());
    }

    public function getNavigationGroup(): ?string
    {
        return $this->evaluate($this->navigationGroup) ?? config('filament-print-node.cluster.navigation_group');
    }

    public function getNavigationLabel(): ?string
    {
        return $this->evaluate($this->navigationLabel) ?? config('filament-print-node.cluster.navigation_label');
    }

    public function getNavigationIcon(): ?string
    {
        return $this->evaluate($this->navigationIcon) ?? config('filament-print-node.cluster.navigation_icon');
    }

    public function getNavigationSort(): ?int
    {
        return $this->evaluate($this->navigationSort) ?? config('filament-print-node.cluster.navigation_sort');
    }

    public function getComputerLabel(): ?string
    {
        return $this->evaluate($this->computerLabel) ?? config('filament-print-node.computers.label');
    }

    public function getComputerPluralLabel(): ?string
    {
        return $this->evaluate($this->computerPluralLabel) ?? config('filament-print-node.computers.plural_label');
    }

    public function getComputerNavigationLabel(): ?string
    {
        return $this->evaluate($this->computerNavigationLabel) ?? config('filament-print-node.computers.navigation_label');
    }

    public function getComputerNavigationIcon(): ?string
    {
        return $this->evaluate($this->computerNavigationIcon) ?? config('filament-print-node.computers.navigation_icon');
    }

    public function getComputerNavigationSort(): ?int
    {
        return $this->evaluate($this->computerNavigationSort) ?? config('filament-print-node.computers.navigation_sort');
    }

    public function getComputerNavigationBadgeCount(): ?int
    {
        return $this->evaluate($this->computerNavigationBadgeCount) ?? config('filament-print-node.computers.navigation_badge_count');
    }

    public function getPrintersLabel(): ?string
    {
        return $this->evaluate($this->printersLabel) ?? config('filament-print-node.printers.label');
    }

    public function getPrintersPluralLabel(): ?string
    {
        return $this->evaluate($this->printersPluralLabel) ?? config('filament-print-node.printers.plural_label');
    }

    public function getPrintersNavigationLabel(): ?string
    {
        return $this->evaluate($this->printersNavigationLabel) ?? config('filament-print-node.printers.navigation_label');
    }

    public function getPrintersNavigationIcon(): ?string
    {
        return $this->evaluate($this->printersNavigationIcon) ?? config('filament-print-node.printers.navigation_icon');
    }

    public function getPrintersNavigationSort(): ?int
    {
        return $this->evaluate($this->printersNavigationSort) ?? config('filament-print-node.printers.navigation_sort');
    }

    public function getPrintersNavigationBadgeCount(): ?int
    {
        return $this->evaluate($this->printersNavigationBadgeCount) ?? config('filament-print-node.printers.navigation_badge_count');
    }

    public function getPrintJobLabel(): ?string
    {
        return $this->evaluate($this->printJobLabel) ?? config('filament-print-node.print_jobs.label');
    }

    public function getPrintJobPluralLabel(): ?string
    {
        return $this->evaluate($this->printJobPluralLabel) ?? config('filament-print-node.print_jobs.plural_label');
    }

    public function getPrintJobNavigationLabel(): ?string
    {
        return $this->evaluate($this->printJobNavigationLabel) ?? config('filament-print-node.print_jobs.navigation_label');
    }

    public function getPrintJobNavigationIcon(): ?string
    {
        return $this->evaluate($this->printJobNavigationIcon) ?? config('filament-print-node.print_jobs.navigation_icon');
    }

    public function getPrintJobNavigationSort(): ?int
    {
        return $this->evaluate($this->printJobNavigationSort) ?? config('filament-print-node.print_jobs.navigation_sort');
    }

    public function getPrintJobNavigationBadgeCount(): ?int
    {
        return $this->evaluate($this->printJobNavigationBadgeCount) ?? config('filament-print-node.print_jobs.navigation_badge_count');
    }

    public function navigationGroup(string | Closure | null $group = null): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationLabel(string | Closure | null $label = null): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function navigationIcon(string | Closure $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function navigationSort(int | Closure $order): static
    {
        $this->navigationSort = $order;

        return $this;
    }

    public function computerResource(
        string | Closure | null $label = null,
        string | Closure | null $pluralLabel = null,
        string | Closure | null $navigationLabel = null,
        string | Closure | null $navigationIcon = null,
        int | Closure | null $navigationSort = null,
        int | Closure | null $navigationBadgeCount = null
    ): static
    {
        $this->setNavigationConfigsAsProperties(resource: 'computer', config: get_defined_vars());

        return $this;
    }

    public function printerResource(
        string | Closure | null $label = null,
        string | Closure | null $pluralLabel = null,
        string | Closure | null $navigationLabel = null,
        string | Closure | null $navigationIcon = null,
        int | Closure | null $navigationSort = null,
        int | Closure | null $navigationBadgeCount = null
    ): static
    {
        $this->setNavigationConfigsAsProperties(resource: 'printers', config: get_defined_vars());

        return $this;
    }

    public function printJobResource(
        string | Closure | null $label = null,
        string | Closure | null $pluralLabel = null,
        string | Closure | null $navigationLabel = null,
        string | Closure | null $navigationIcon = null,
        int | Closure | null $navigationSort = null,
        int | Closure | null $navigationBadgeCount = null
    ): static
    {
        $this->setNavigationConfigsAsProperties(resource: 'printJob', config: get_defined_vars());

        return $this;
    }

    public function setNavigationConfigsAsProperties(string $resource, array $config): void
    {
        foreach($config as $key => $value) {
            $this->{$resource.ucfirst($key)} = $value; 
        }
    }
 
    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                //
            ])
            ->pages([
                //
            ])
            ->discoverClusters(in: __DIR__.'\Clusters', for: 'Consignr\\FilamentPrintNode\\Clusters');
    }
 
    public function boot(Panel $panel): void
    {
        //
    }
}