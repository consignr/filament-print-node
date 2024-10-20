<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconSize;
use Illuminate\Support\Facades\Http;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\Group;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Consignr\FilamentPrintNode\Models\Computer;
use Consignr\FilamentPrintNode\Clusters\PrintNode;
use Consignr\FilamentPrintNode\Enums\ComputerState;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Consignr\FilamentPrintNode\FilamentPrintNodePlugin;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\ComputerResource\Pages;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\ComputerResource\RelationManagers;

class ComputerResource extends Resource
{
    protected static ?string $model = Computer::class;

    protected static ?string $cluster = PrintNode::class;

    public static function getLabel(): string
    {
        return FilamentPrintNodePlugin::get()->getComputerLabel();
    }

    public static function getPluralLabel(): string
    {
        return FilamentPrintNodePlugin::get()->getComputerPluralLabel();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentPrintNodePlugin::get()->getComputerNavigationLabel();
    }

    public static function getNavigationIcon(): string
    {
        return FilamentPrintNodePlugin::get()->getComputerNavigationIcon();
    }

    public static function getNavigationSort(): int
    {
        return FilamentPrintNodePlugin::get()->getComputerNavigationSort();
    }

    public static function getNavigationBadge(): ?string
    {
        return FilamentPrintNodePlugin::get()->getComputerNavigationBadgeCount();
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
                        ->columns(2)
                        ->schema([
                            TextEntry::make('name'),
                            TextEntry::make('inet')->label('IP Address'),
                            TextEntry::make('state')
                                ->color('default')
                                ->iconColor(fn (ComputerState $state): string => $state->getColor()),
                            TextEntry::make('hostname'),
                            TextEntry::make('version')->placeholder('-'),
                            TextEntry::make('inet6')->label('IPv6 Address')->placeholder('-'),
                            TextEntry::make('jre')->label('Java Runtime Env.')->placeholder('-'),
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('inet')
                    ->label('IP Address')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('state')
                    ->sortable()
                    ->color('default')
                    ->iconColor(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon()),
                TextColumn::make('inet6')
                    ->label('IPv6 Address')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('hostname')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('version')
                    ->sortable(),
                TextColumn::make('jre')
                    ->label('Java Runtime Env.')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createTimestamp')
                    ->dateTime()
                        ->sortable()
                    ->label('Created at'),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->hiddenLabel(),
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
