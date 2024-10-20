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
use Consignr\FilamentPrintNode\Models\PrintJob;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Consignr\FilamentPrintNode\Clusters\PrintNode\Resources\PrintJobResource;
use Consignr\FilamentPrintNode\FilamentPrintNodePlugin;

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
                Tables\Actions\Action::make('cancel_all_printer_print_job_set')
                    ->action(function (Tables\Actions\Action $action) {                          
                        $cancelResponse = Http::withBasicAuth(env('PRINTNODE_API_KEY'), env('PRINTNODE_PASSWORD'))
                            ->delete("https://api.printnode.com/printers/{$this->ownerRecord->id}/printjobs");
                        
                        session([$action->getName() => count($cancelResponse->json())]);    
                            
                        if ($cancelResponse->ok() && filled($cancelResponse->json())) {
                            $action->success();
                        }

                        if ($cancelResponse->ok() && empty($cancelResponse->json())) {
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
