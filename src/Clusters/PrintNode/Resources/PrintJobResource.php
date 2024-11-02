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
                Action::make('state_history')
                    ->action(fn () => null) 
                    ->iconButton()
                    ->icon('heroicon-s-clock')
                    ->color('info')
                    ->modalWidth(MaxWidth::SixExtraLarge)
                    ->modalSubmitAction(false)   
                    ->modalCancelAction(false)                        
                    ->infolist(function (PrintJob $record, $infolist) {

                        $printNode = new PrintNode(env('PRINTNODE_API_KEY'));

                        $response = $printNode->send(new GetPrintJobsStates(printJobSet: [$record->id]));

                            if ($response->ok()) {                            
                                $state = ['state' => $response->json()[0]];
                                $infolist->state($state);
                            } 

                            if (! $response->ok()) {   
                                Notification::make()
                                    ->warning()
                                    ->title('Oops, Something went wrong!')  
                                    ->body('The state history could not be retrieved.')   
                                    ->send();
                                                        
                                return null;
                            }

                        return [
                            Group::make([
                                TextEntry::make('age')->state(null),                                
                                TextEntry::make('created_at')->state(null)->columnSpan(2)->alignCenter(),
                                TextEntry::make('message')->state(null)->columnSpan(7),
                                TextEntry::make('status')->state(null)->columnSpan(2)
                            ])->columns(12),
                            RepeatableEntry::make('state')
                                ->hiddenLabel()
                                ->columns(12)
                                ->schema([
                                    TextEntry::make('age')
                                        ->hiddenLabel()
                                        ->suffix('ms'),                                    
                                    TextEntry::make('createTimestamp')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn ($state): string => Carbon::parse($state)->format('d M Y H:i:s'))
                                        ->columnSpan(2),
                                    TextEntry::make('message')
                                        ->hiddenLabel()
                                        ->columnSpan(7)
                                        ->placeholder('-'),
                                    TextEntry::make('state')
                                        ->formatStateUsing(fn (string $state): PrintJobState => PrintJobState::tryFrom($state))
                                        ->hiddenLabel()
                                        ->columnSpan(2)
                                        ->badge()
                                        ->color(fn (string $state): string => PrintJobState::tryFrom($state)->getColor())
                                        
                                ])                                
                            ];
                    }),
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
