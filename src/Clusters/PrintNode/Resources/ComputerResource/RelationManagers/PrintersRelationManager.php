<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\ComputerResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Consignr\FilamentPrintNode\FilamentPrintNodePlugin;
use Filament\Resources\RelationManagers\RelationManager;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource;

class PrintersRelationManager extends RelationManager
{
    protected static string $relationship = 'printers';

    protected static ?string $badgeColor = 'info';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return FilamentPrintNodePlugin::get()->getPrintersPluralLabel();
    }
 
    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->printers()->count();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return PrinterResource::infolist($infolist);
    }

    public function form(Form $form): Form
    {
        return PrinterResource::form($form);
    }

    public function table(Table $table): Table
    {
        return PrinterResource::table($table);
    }
}
