<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;

use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
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
                TextColumn::make('name')
                    ->description(fn (Printer $record): string => $record->description)
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
