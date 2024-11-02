<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources;

use Exception;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Http;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\Group;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Consignr\FilamentPrintNode\Api\PrintNode;
use Consignr\FilamentPrintNode\Models\PrintJob;
use Filament\Infolists\Components\RepeatableEntry;
use Consignr\FilamentPrintNode\Enums\PrintJobState;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Consignr\FilamentPrintNode\FilamentPrintNodePlugin;
use Consignr\FilamentPrintNode\Actions as PrintNodeActions;
use Consignr\FilamentPrintNode\Clusters\PrintNode as PrintNodeCluster;
use Consignr\FilamentPrintNode\Api\Requests\PrintJobs\DeletePrintJobsSet;
use Consignr\FilamentPrintNode\Api\Requests\PrintJobs\GetPrintJobsStates;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource\Pages;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource\RelationManagers;

class PrintJobResource extends Resource
{
    protected static ?string $model = PrintJob::class;

    protected static ?string $cluster = PrintNodeCluster::class;

    public static function getLabel(): string
    {
        return FilamentPrintNodePlugin::get()->getPrintJobLabel();
    }

    public static function getPluralLabel(): string
    {
        return FilamentPrintNodePlugin::get()->getPrintJobPluralLabel();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentPrintNodePlugin::get()->getPrintJobNavigationLabel();
    }

    public static function getNavigationIcon(): string
    {
        return FilamentPrintNodePlugin::get()->getPrintJobNavigationIcon();
    }

    public static function getNavigationSort(): int
    {
        return FilamentPrintNodePlugin::get()->getPrintJobNavigationSort();
    }

    public static function getNavigationBadge(): ?string
    {
        return FilamentPrintNodePlugin::get()->getPrintJobNavigationBadgeCount();
    }    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contentType')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source'),
                TextColumn::make('state')
                    ->color(fn (PrintJobState $state): string => $state->getColor())
                    ->badge(),
                TextColumn::make('computer_name')
                    ->searchable()
                    ->sortable()
                    ->label('Computer')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('printer_desc')
                    ->label('Printer')
                    ->description(fn (PrintJob $record): string => $record->printer_name)
                    ->searchable(['printer_name', 'printer_desc'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expireAt')
                    ->dateTime()
                    ->label('Expires at')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createTimestamp')
                    ->dateTime()
                    ->label('Created at')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                PrintNodeActions\ViewStateHistoryAction::make(),
                PrintNodeActions\CancelPrintJobAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrintJobs::route('/'),
        ];
    }
}
