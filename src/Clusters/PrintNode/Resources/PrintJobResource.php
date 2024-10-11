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
use Consignr\FilamentPrintNode\Models\PrintJob;
use Consignr\FilamentPrintNode\Clusters\PrintNode;
use Filament\Infolists\Components\RepeatableEntry;
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
                Action::make('state_history')
                    ->action(fn () => null) 
                    ->iconButton()
                    ->icon('heroicon-s-clock')
                    ->color('info')
                    ->modalWidth(MaxWidth::SixExtraLarge)
                    ->modalSubmitAction(false)   
                    ->modalCancelAction(false)                        
                    ->infolist(function (PrintJob $record, $infolist) {

                            $stateResponse = Http::withBasicAuth(env('PRINTNODE_API_KEY'), env('PRINTNODE_PASSWORD'))
                                ->get("https://api.printnode.com/printjobs/{$record->id}/states");

                            if ($stateResponse->ok()) {                            
                                $state = ['state' => $stateResponse->json()[0]];
                                $infolist->state($state);
                            } 

                            if (! $stateResponse->ok()) {   
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
                Tables\Actions\Action::make('cancel_print_job_set')
                    ->action(function (PrintJob $record, Tables\Actions\Action $action) {
                        
                        $cancelRequest = Http::withBasicAuth(env('PRINTNODE_API_KEY'), env('PRINTNODE_PASSWORD'))
                            ->delete('https://api.printnode.com/printjobs'[$record->id]);

                        if ($cancelRequest->ok() && filled($cancelRequest->json())) {
                            $action->success();
                        }
                    })
                    ->disabled(fn (PrintJob $record): bool => $record->state === PrintJobState::Done)
                    ->requiresConfirmation()
                    ->label('Cancel print job')
                    ->modalDescription('Are you sure you\'d like to cancel this print job?')
                    ->modalSubmitActionLabel('cancel')
                    ->icon('heroicon-s-x-circle')
                    ->iconButton()
                    ->color('danger')
                    ->successNotificationTitle('Print Job Cancelled')
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
