<?php

namespace Consignr\FilamentPrintNode\Actions;

use Consignr\FilamentPrintNode\Api;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Consignr\FilamentPrintNode\Models\PrintJob;
use Consignr\FilamentPrintNode\Enums\PrintJobState;

class CancelPrintJobAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'cancel_print_job_set_table_action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Cancel');

        $this->icon('heroicon-s-x-circle');

        $this->color('danger');

        $this->requiresConfirmation();

        $this->modalDescription('Are you sure you\'d like to cancel this print job?');

        $this->modalSubmitActionLabel('Proceed');
        
        $this->successNotificationTitle('Print Job(s) cancelled');  
        
        $this->failureNotification(
            Notification::make()
                ->warning()
                ->title('Print job could not be cancelled')
                ->body('Print jobs which have been completed or have been delivered to the PrintNode Client cannot be cancelled.')
        );
        
        $this->disabled(fn (PrintJob $record): bool => $record->state === PrintJobState::Done);
        
        $this->action(function (PrintJob $record, CancelPrintJobsAction $action) {

            $printNode = new Api\PrintNode(env('PRINTNODE_API_KEY'));

            $response = $printNode->send(new Api\Requests\PrintJobs\DeletePrintJobsSet(printJobSet: [$record->id]));
           
            if ($response->ok() && filled($response->json())) {
                $action->success();
            }

            if ($response->ok() && empty($response->json())) {
                $action->failure();
            }
        });
    }
}
