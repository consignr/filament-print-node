<?php

namespace Consignr\FilamentPrintNode\Actions;

use Carbon\Carbon;
use Consignr\FilamentPrintNode\Api;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Infolists\Components\Group;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\TextEntry;
use Consignr\FilamentPrintNode\Models\PrintJob;
use Filament\Infolists\Components\RepeatableEntry;
use Consignr\FilamentPrintNode\Enums\PrintJobState;

class ViewStateHistoryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'view_state_history_table_action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn () => null);
         
        $this->label('State History');
        
        $this->icon('heroicon-s-clock');
        
        $this->color('info');
        
        $this->modalWidth(MaxWidth::SixExtraLarge);
        
        $this->modalSubmitAction(false);
           
        $this->modalCancelAction(false);
        
        $this->infolist(function (PrintJob $record, $infolist) {

            $printNode = new Api\PrintNode(env('PRINTNODE_API_KEY'));

            $response = $printNode->send(new Api\Requests\PrintJobs\GetPrintJobsStates(printJobSet: [$record->id]));

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
        });
    }
}
