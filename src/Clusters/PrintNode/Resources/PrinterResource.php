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
use Consignr\FilamentPrintNode\Models\Printer;
use Consignr\FilamentPrintNode\Clusters\PrintNode;
use Consignr\FilamentPrintNode\Enums\PrinterState;
use Filament\Resources\RelationManagers\RelationManager;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\Pages;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\RelationManagers;

class PrinterResource extends Resource
{
    protected static ?string $model = Printer::class;

    protected static ?string $navigationIcon = 'heroicon-o-printer';

    protected static ?string $cluster = PrintNode::class;

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
                Tables\Actions\Action::make('capabilities')
                    ->icon('heroicon-s-information-circle')
                    ->iconButton()
                    ->color('info')
                    ->slideOver()
                    ->infolist([
                        Tabs::make('Capabilities')
                            ->contained(false)
                            ->columnSpanFull()
                            ->schema([
                                Tab::make('General')
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('tray_names')->listWithLineBreaks()->separator(',')->placeholder('-'),
                                        TextEntry::make('supported_dpis')->listWithLineBreaks()->label('Supported DPI\'s'),
                                        IconEntry::make('collate')->boolean()->label('Collation Support'),
                                        TextEntry::make('copies')->label('Maximum copies'),
                                        IconEntry::make('color')->boolean()->label('Colour Support'),                                
                                        IconEntry::make('duplex')->boolean()->label('Duplex Support'),                
                                        TextEntry::make('n_up_printing')->listWithLineBreaks()->separator(',')->placeholder('-'), 
                                        TextEntry::make('printrate')
                                            ->formatStateUsing(fn (?array $state): float => $state ? $state['rate'] : null)
                                            ->suffix(fn (?array $state): float => $state ? $state['unit'] : null)
                                            ->placeholder('-'),
                                        IconEntry::make('supports_custom_paper_size')->boolean(),
                                        Fieldset::make('Supported Width')
                                            ->schema([
                                                TextEntry::make('minimum_supported_width')->formatStateUsing(fn (int $state): int => $state/10)->suffix('mm')->label('Minimum'),
                                                TextEntry::make('maximum_supported_width')->formatStateUsing(fn (int $state): int => $state/10)->suffix('mm')->label('Maximum'),
                                            ]),
                                        Fieldset::make('Supported Height')
                                            ->schema([
                                                TextEntry::make('minimum_supported_height')->formatStateUsing(fn (int $state): int => $state/10)->suffix('mm')->label('Minimum'),                                
                                                TextEntry::make('maximum_supported_height')->formatStateUsing(fn (int $state): int => $state/10)->suffix('mm')->label('Maximum'),
                                            ]),
                                    ]),
                                Tab::make('Media')
                                    ->schema([
                                        TextEntry::make('media_names')->listWithLineBreaks()->separator(',')->placeholder('-')
                                    ]),
                                Tab::make('Papers')
                                    ->schema(function ($record) {
                                        $papers = [];
                                        foreach ($record->papers as $key => $paper) {
                                            $papers[] = Section::make(null)
                                                ->description($key)
                                                ->collapsible()
                                                ->collapsed()
                                                ->compact()
                                                ->columns(2)
                                                ->schema([
                                                    TextEntry::make('width')
                                                        ->getStateUsing(fn () => $paper['width'])
                                                        ->formatStateUsing(fn (int $state): int => $state/10)
                                                        ->suffix('mm'),
                                                    TextEntry::make('height')
                                                        ->getStateUsing(fn () => $paper['height'])
                                                        ->formatStateUsing(fn (int $state): int => $state/10)
                                                        ->suffix('mm')
                                                ]);
                                        }

                                        return $papers;
                                    }),
                                ])
                    ])
                    ->modalHeading('Printer Capabilities')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalWidth(MaxWidth::Large),
                Tables\Actions\Action::make('cancel_printer_print_job_set')
                    ->action(function (Printer $record, Tables\Actions\Action $action) {

                        $cancelResponse = Http::withBasicAuth(env('PRINTNODE_API_KEY'), env('PRINTNODE_PASSWORD'))
                            ->delete("https://api.printnode.com/printers/{$record->id}/printjobs");

                        if ($cancelResponse->ok()) {
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
