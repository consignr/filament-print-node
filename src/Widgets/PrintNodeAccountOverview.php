<?php

namespace Consignr\FilamentPrintNode\Widgets;

use Filament\Widgets\Widget;
use Filament\Infolists\Infolist;
use Consignr\FilamentPrintNode\Api;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Consignr\FilamentPrintNode\Models\Computer;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Concerns\InteractsWithInfolists;

class PrintNodeAccountOverview extends Widget implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    protected int | string | array $columnSpan = 2;
    
    protected static string $view = 'filament-print-node::widgets.print-node-account-overview';

    public array $response;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->response)
            ->schema([
                Section::make('Account')
                    ->description('Print Node account details')
                    ->headerActions([
                        Action::make('check_credentials')
                            ->action(function (Action $action) {
                                $printNode = new Api\PrintNode(env('PRINTNODE_API_KEY'));

                                $response = $printNode->send(new Api\Requests\GetNoop);

                                if ($response->ok()) {
                                    $action->success();
                                } 
                                
                                if ($response->status() === 401) {
                                    $action->failure();
                                }
                            })
                            ->successNotification(
                                Notification::make()
                                    ->success()
                                    ->title('200 OK')
                                    ->body('The provided credentials are valid.')
                            )
                            ->failureNotification(
                                Notification::make()
                                    ->danger()
                                    ->title('401 Unauthorized')
                                    ->body('The provided credentials are invalid. Please check your API key and try again.')
                            ),
                        Action::make('ping')
                            ->action(function (Action $action) {
                                $printNode = new Api\PrintNode(env('PRINTNODE_API_KEY'));

                                $response = $printNode->send(new Api\Requests\GetPing);

                                if ($response->ok()) {
                                    $action->success();
                                } else {
                                    $action->failure();
                                }
                            })
                            ->successNotification(
                                Notification::make()
                                    ->success()
                                    ->title('200 OK')
                                    ->body('Print Node service responded successfully')
                            )
                            ->failureNotification(
                                Notification::make()
                                    ->danger()
                                    ->title('Service Unavailable')
                                    ->body('Please try again later.')
                            ),
                    ])
                    ->schema([
                        TextEntry::make('owner')
                            ->getStateUsing(fn (): string => "{$this->response['firstname']} {$this->response['lastname']}")
                            ->inlineLabel(),                        
                        TextEntry::make('email')
                            ->inlineLabel(),
                        TextEntry::make('id')
                            ->label('Account ID')
                            ->inlineLabel(),
                        TextEntry::make('state')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => ucfirst($state))
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'suspended' => 'danger',
                            })
                            ->inlineLabel(),
                        TextEntry::make('totalPrints')
                            ->label('Total Prints')
                            ->inlineLabel(),
                        TextEntry::make('numComputers')
                            ->label('Computers')
                            ->inlineLabel(),
                        TextEntry::make('credits')
                            ->default('-')
                            ->inlineLabel(),
                        IconEntry::make('canCreateSubAccounts')
                            ->label('Integrator Account')
                            ->helperText(fn (bool $state): string => $state ? 'Can create sub-accounts' : 'Cannot create sub-accounts')
                            ->boolean()
                            ->inlineLabel(),
                        TextEntry::make('creatorEmail')
                            ->default('-')
                            ->inlineLabel(),
                        TextEntry::make('creatorRef')
                            ->default('-')
                            ->inlineLabel(),
                        TextEntry::make('childAccounts')
                            ->listWithLineBreaks()
                            ->default('-')
                            ->inlineLabel(),
                        TextEntry::make('connected')
                            ->listWithLineBreaks()
                            ->default('-')
                            ->inlineLabel()
                            ->state(function (): array {                                                            
                                return Computer::whereIn('id', $this->response['connected'])->pluck('name')->toArray();
                            }),
                        TextEntry::make('tags')
                            ->listWithLineBreaks()
                            ->default('-')
                            ->inlineLabel(),
                        TextEntry::make('permissions')
                            ->listWithLineBreaks()
                            ->default('-')
                            ->inlineLabel(),
                    ])
                
            ]);
    }
}
