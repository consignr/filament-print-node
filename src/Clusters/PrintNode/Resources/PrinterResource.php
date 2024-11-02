<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Blade;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\Group;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Consignr\FilamentPrintNode\Api\PrintNode;
use Consignr\FilamentPrintNode\Models\Printer;
use Consignr\FilamentPrintNode\Enums\PrinterState;
use Consignr\FilamentPrintNode\Api\Requests\PrintJobs;
use Consignr\FilamentPrintNode\FilamentPrintNodePlugin;
use Filament\Resources\RelationManagers\RelationManager;
use Consignr\FilamentPrintNode\Clusters\PrintNode as PrintNodeCluster;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\Pages;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\RelationManagers;

class PrinterResource extends Resource
{
    protected static ?string $model = Printer::class;

    protected static ?string $cluster = PrintNodeCluster::class;

    public static function getLabel(): string
    {
        return FilamentPrintNodePlugin::get()->getPrintersLabel();
    }

    public static function getPluralLabel(): string
    {
        return FilamentPrintNodePlugin::get()->getPrintersPluralLabel();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentPrintNodePlugin::get()->getPrintersNavigationLabel();
    }

    public static function getNavigationIcon(): string
    {
        return FilamentPrintNodePlugin::get()->getPrintersNavigationIcon();
    }

    public static function getNavigationSort(): int
    {
        return FilamentPrintNodePlugin::get()->getPrintersNavigationSort();
    }

    public static function getNavigationBadge(): ?string
    {
        return FilamentPrintNodePlugin::get()->getPrintersNavigationBadgeCount();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Group::make([
                    Section::make('View')
                        ->columnSpan(2)
                        ->heading(null)
                        ->columns(3)
                        ->schema([
                            TextEntry::make('name'),
                            TextEntry::make('description'),
                            TextEntry::make('computer.name')
                                ->helperText(fn (Printer $record): string => $record->computer->inet),
                            TextEntry::make('state')
                                ->color('default')
                                ->iconColor(fn (PrinterState $state): string => $state->getColor()),
                            IconEntry::make('default')
                                ->boolean(),
                        ]),
                    Section::make('ViewCreated')
                        ->columnSpan(1)
                        ->heading(null)
                        ->schema([                            
                            TextEntry::make('createTimestamp')
                                ->dateTime()
                                ->helperText(fn (Carbon $state) => $state->diffForHumans())
                                ->label('Created At'),
                        ])
                ])
                ->columnSpanFull()
                ->columns(3)
             ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')
                    ->description(description: function (Printer $record) { 
                        if ($record->default) {
                            return new HtmlString("<div class='flex gap-3 align-top'><p class='text-sm text-gray-500 dark:text-gray-400'>".$record->name."</p>".Blade::render('<x-filament::badge color="info">Default</x-filament::badge></div>'));
                        }

                        return $record->name;                        
                    }, position: 'below')
                    ->searchable(),
                TextColumn::make('state')
                    ->color('default')
                    ->iconColor(fn (PrinterState $state): string => $state->getColor())
                    ->icon(fn (PrinterState $state): string => $state->getIcon()),
                TextColumn::make('createTimestamp')
                    ->dateTime()
                    ->label('Created at')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton(),
                Tables\Actions\Action::make('cancel_printer_print_job_set')
                    ->action(function (Printer $record, Tables\Actions\Action $action) {

                        $printNode = new PrintNode(env('PRINTNODE_API_KEY'));

                        $response = $printNode->send(new PrintJobs\DeletePrintJobsOfPrintersSet(printerSet: [$record->id]));

                        if ($response->ok()) {
                            $action->success();
                        }
                    })
                    ->requiresConfirmation()
                    ->label('Cancel all print jobs')
                    ->modalDescription('Are you sure you\'d like to cancel all print jobs for this printer?')
                    ->modalSubmitActionLabel('Proceed')
                    ->icon('heroicon-s-x-circle')
                    ->iconButton()
                    ->color('danger')
                    ->successNotificationTitle('Print jobs cancelled')
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
            RelationManagers\PrintJobsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrinters::route('/'),
            'view' => Pages\ViewPrinter::route('/{record}')
        ];
    }
}
