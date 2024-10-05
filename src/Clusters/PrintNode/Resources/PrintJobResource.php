<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Consignr\FilamentPrintNode\Models\PrintJob;
use Consignr\FilamentPrintNode\Clusters\PrintNode;
use Consignr\FilamentPrintNode\Enums\PrintJobState;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource\Pages;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource\RelationManagers;

class PrintJobResource extends Resource
{
    protected static ?string $model = PrintJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contentType')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListPrintJobs::route('/'),
        ];
    }
}
