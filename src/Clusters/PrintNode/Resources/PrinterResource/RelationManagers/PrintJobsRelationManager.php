<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Consignr\FilamentPrintNode\Models\PrintJob;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource;

class PrintJobsRelationManager extends RelationManager
{
    protected static string $relationship = 'printJobs';

    protected static ?string $badgeColor = 'info';
 
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
        return PrintJob::table($table);
    }
}
