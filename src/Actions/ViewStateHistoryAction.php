<?php

namespace Consignr\FilamentPrintNode\Actions;

use Carbon\Carbon;
use Consignr\FilamentPrintNode\Api;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\MaxWidth;
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

            $printNode = new Api\PrintNode(config('filament-print-node.api_key'));

            $response = $printNode->send(new Api\Requests\PrintJobs\GetPrintJobsStates(printJobSet: [$record->id]));

            if ($response->ok()) {   

                $headings = [
                    'age' => 'Age',
                    'clientVersion' => 'Client Version',
                    'createTimestamp' => 'Created At',
                    'data' => 'Data',
                    'message' => 'Message',
                    'printJobId' => 'Print Job ID',
                    'state' => 'State',
                ];  

                $state = ['state' => collect($response->collect()->first())->prepend($headings)->toArray()];
                
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
                RepeatableEntry::make('state')
                    ->view('filament-print-node::state-history-repeatable-entry')
                    ->hiddenLabel()
                    ->columns(12)
                    ->schema([
                        TextEntry::make('age')
                            ->hiddenLabel()
                            ->suffix(fn (string $state): ?string => $state === 'Age' ? null : 'ms'),                                    
                        TextEntry::make('createTimestamp')
                            ->hiddenLabel()
                            ->formatStateUsing(fn ($state): string => $state === 'Created At' ? $state : Carbon::parse($state)->format('d M Y H:i:s'))
                            ->columnSpan(2),
                        TextEntry::make('message')
                            ->hiddenLabel()
                            ->columnSpan(7)
                            ->placeholder('-'),
                        TextEntry::make('state')
                            ->formatStateUsing(fn (string $state): string => $state === 'State' ? $state : PrintJobState::tryFrom($state)->getLabel())
                            ->hiddenLabel()
                            ->columnSpan(2)
                            ->badge(fn (string $state): bool => $state === 'State' ? false : true)
                            ->color(fn (string $state): string => $state === 'State' ? $state : PrintJobState::tryFrom($state)->getColor())
                            
                    ])                                
                ];
        });
    }
}
