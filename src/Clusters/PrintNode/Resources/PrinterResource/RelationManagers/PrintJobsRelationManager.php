<?php

namespace Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrinterResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Consignr\FilamentPrintNode\Api\PrintNode;
use Consignr\FilamentPrintNode\Models\PrintJob;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Consignr\FilamentPrintNode\FilamentPrintNodePlugin;
use Filament\Resources\RelationManagers\RelationManager;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource;
use Consignr\FilamentPrintNode\Api\Requests\PrintJobs;

class PrintJobsRelationManager extends RelationManager
{
    protected static string $relationship = 'printJobs';

    protected static ?string $badgeColor = 'info';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return FilamentPrintNodePlugin::get()->getPrintJobPluralLabel();
    }
 
    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->printJobs()->count();
    }

    public function form(Form $form): Form
    {
        return PrintJobResource::form($form);
    }

    public function table(Table $table): Table
    {
        return PrintJobResource::table($table)
            ->headerActions([
                Tables\Actions\Action::make('cancel_all_print_jobs_for_printer_set')
                    ->action(function (Tables\Actions\Action $action) {                          
                        $printNode = new PrintNode(env('PRINTNODE_API_KEY'));

                        $response = $printNode->send(new PrintJobs\DeletePrintJobsOfPrintersSet(printerSet: [$this->ownerRecord->getKey()]));
                        
                        session([$action->getName() => count($response->json())]);    
                            
                        if ($response->ok() && filled($response->json())) {
                            $action->success();
                        }

                        if ($response->ok() && empty($response->json())) {
                            $action->failure();
                        }
                    })
                    ->disabled(fn (): bool => $this->ownerRecord->printJobs()->count() === 0)
                    ->requiresConfirmation()
                    ->label('Cancel All')
                    ->modalDescription('Are you sure you\'d like to cancel all print jobs for this printer?')
                    ->modalSubmitActionLabel('Proceed')
                    ->icon('heroicon-s-x-circle')
                    ->link()
                    ->color('danger')
                    ->failureNotification(
                        Notification::make()
                            ->warning()
                            ->title('0 Print jobs cancelled')
                            ->body('Print jobs which have been completed or have been delivered to the PrintNode Client cannot be cancelled.')
                    )
                    ->successNotificationTitle(function (Tables\Actions\Action $action) {
                        $count = session($action->getName());
                        return $count.' Print jobs cancelled';                                               
                    }),
            ]);
    }
}
