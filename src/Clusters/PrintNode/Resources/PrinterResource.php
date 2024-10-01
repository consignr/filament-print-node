<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;

use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Consignr\FilamentPrintNode\Models\Printer;
use Consignr\FilamentPrintNode\Clusters\PrintNode;
use Consignr\FilamentPrintNode\Enums\PrinterState;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                //
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
