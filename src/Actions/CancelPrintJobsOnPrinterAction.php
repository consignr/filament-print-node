<?php

namespace Consignr\FilamentPrintNode\Actions;

use Closure;
use Consignr\FilamentPrintNode\Api;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Consignr\FilamentPrintNode\Models\Printer;

class CancelPrintJobsOnPrinterAction extends Action
{
    protected Printer | Closure | null $printer = null;

    public function printer(Printer | Closure $printer): static
    {
        $this->printer = $printer;

        return $this;
    }

    public function getPrinter(): ?Printer
    {
        return $this->evaluate($this->printer);
    }

    public static function getDefaultName(): ?string
    {
        return 'cancel_print_job_set_on_printer_table_action';
    }

    protected function setUp(): void
    {
        parent::setUp();     

        $this->label('Clear Queue');

        $this->icon('heroicon-s-x-circle');

        $this->color('danger');

        $this->requiresConfirmation();

        $this->modalDescription('Are you sure you\'d like to cancel all print jobs for this printer?');

        $this->modalSubmitActionLabel('Proceed');
        
        $this->failureNotification(
            Notification::make()
                ->warning()
                ->title('0 Print jobs cancelled')
                ->body('Print jobs which have been completed or have been delivered to the PrintNode Client cannot be cancelled.')
        );

        $this->successNotificationTitle(function (CancelPrintJobsOnPrinterAction $action) {
            $count = session($action->getName());
            return $count.' Print jobs cancelled';                                               
        });
        
        $this->disabled(fn (): bool => $this->getPrinter()?->printJobs()->count() === 0);
        
        $this->action(function (CancelPrintJobsOnPrinterAction $action) {                          
            $printNode = new Api\PrintNode(env('PRINTNODE_API_KEY'));

            $response = $printNode->send(new Api\Requests\PrintJobs\DeletePrintJobsOfPrintersSet(printerSet: [$this->getPrinter()->getKey()]));
            
            session([$action->getName() => count($response->json())]);    
                
            if ($response->ok() && filled($response->json())) {
                $action->success();
            }

            if ($response->ok() && empty($response->json())) {
                $action->failure();
            }
        });
    }
}
