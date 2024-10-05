<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Consignr\FilamentPrintNode\Models\Printer;
use Consignr\FilamentPrintNode\Clusters\PrintNode;
use Consignr\FilamentPrintNode\Enums\PrinterState;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\Pages;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\RelationManagers;
use Filament\Support\Enums\MaxWidth;

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
                Tables\Actions\ViewAction::make()
                    ->label('Capabilities')
                    ->icon('heroicon-o-information-circle')
                    ->slideOver()
                    ->modalHeading('Printer Capabilities')
                    ->modalWidth(MaxWidth::Large)
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
            'index' => Pages\ListPrinters::route('/'),
        ];
    }
}
