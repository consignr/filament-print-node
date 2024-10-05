<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconSize;
use Illuminate\Support\Facades\Http;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Consignr\FilamentPrintNode\Models\Computer;
use Consignr\FilamentPrintNode\Clusters\PrintNode;
use Consignr\FilamentPrintNode\Enums\ComputerState;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\ComputerResource\Pages;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\ComputerResource\RelationManagers;
use Filament\Notifications\Notification;

class ComputerResource extends Resource
{
    protected static ?string $model = Computer::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

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
                    ->searchable(),
                TextColumn::make('inet')
                    ->searchable(),
                TextColumn::make('state')
                    ->color('default')
                    ->iconColor(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon()),
                TextColumn::make('inet6')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('hostname')
                    ->searchable(),
                TextColumn::make('version'),
                TextColumn::make('jre')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createTimestamp')
                    ->dateTime()
                    ->label('Created at'),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('delete_computer_set')
                    ->action(function ($record, $livewire) {
                        
                        $deleteRequest = Http::withBasicAuth(env('PRINTNODE_API_KEY'), env('PRINTNODE_PASSWORD'))
                            ->delete('https://api.printnode.com/computers'[$record->id]);

                        if ($deleteRequest->successful()) {
                            $livewire->success();
                        }
                    })
                    ->requiresConfirmation()
                    ->label('Delete computer')
                    ->modalDescription('Are you sure you\'d like to delete this computer from PrintNode?')
                    ->modalSubmitActionLabel('Delete')
                    ->icon('heroicon-m-trash')
                    ->iconButton()
                    ->color('danger')
                    ->successNotificationTitle('Computer Deleted')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PrintersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComputers::route('/'),
            'view' => Pages\ViewComputer::route('/{record}')
        ];
    }
}
