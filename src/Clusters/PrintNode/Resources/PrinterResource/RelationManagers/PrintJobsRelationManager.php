<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Consignr\FilamentPrintNode\FilamentPrintNodePlugin;
use Filament\Resources\RelationManagers\RelationManager;
use Consignr\FilamentPrintNode\Actions as PrintNodeActions;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource;

class PrintJobsRelationManager extends RelationManager
{
    protected static string $relationship = 'printJobs';

    protected static ?string $badgeColor = 'info';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return FilamentPrintNodePlugin::get()->getPrintJobPluralLabel();
    }
 
    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->printJobs()->count();
    }

    public function form(Form $form): Form
    {
        return PrintJobResource::form($form);
    }

    public function table(Table $table): Table
    {
        return PrintJobResource::table($table)
            ->headerActions([
                PrintNodeActions\CancelPrintJobsOnPrinterAction::make()->printer($this->ownerRecord),               
            ]);
    }
}
