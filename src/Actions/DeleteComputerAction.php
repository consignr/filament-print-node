<?php

namespace Consignr\FilamentPrintNode\Actions;

use Consignr\FilamentPrintNode\Api;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class DeleteComputerAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'delete_computer_table_action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Delete');

        $this->icon('heroicon-s-trash');

        $this->color('danger');

        $this->requiresConfirmation();

        $this->modalDescription('Are you sure you\'d like to delete this computer from PrintNode?');

        $this->modalSubmitActionLabel('Delete');
        
        $this->successNotificationTitle('Computer Deleted');  
        
        $this->failureNotification(
            Notification::make()
                ->danger()
                ->title('Oops, Something went wrong.')
                ->body('Computer could not be deleted')
        );
        
        $this->action(function ($record, $livewire) {

            $printNode = new Api\PrintNode(config('filament-print-node.api_key'));

            $response = $printNode->send(new Api\Requests\Computers\DeleteComputersSet(computerSet: [$record->id]));

            if ($response->successful()) {
                $livewire->success();
            }

            if ($response->failed()) {
                $livewire->failure();
            }
        });
    }
}
