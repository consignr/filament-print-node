<?php

namespace Consignr\FilamentPrintNode\Actions;

use Closure;
use Illuminate\Support\Str;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentIcon;
use Consignr\FilamentPrintNode\Api\PrintNode;
use Consignr\FilamentPrintNode\Enums\ContentType;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Consignr\FilamentPrintNode\Api\Requests\PrintJobs;

class CreatePrintJobAction extends Action
{
    use CanCustomizeProcess;

    protected int | Closure | null $printerId = null;

    protected ContentType | null $contentType = null;

    protected string | Closure | null $content = null;

    protected string | Closure | null $title = null;

    protected string | Closure | null $source = null;

    protected int | Closure $expireAfter = 1209600;

    protected int | Closure $quantity = 1;

    public function printerId(int | Closure $id): static
    {
        $this->printerId = $id;

        return $this;
    }

    public function contentType(ContentType $contentType): static
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function content(string | Closure $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function title(string | Closure $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function source(string | Closure $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function expireAfter(int | Closure $expireAfter): static
    {
        $this->expireAfter = $expireAfter;

        return $this;
    }

    public function quantity(int | Closure $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrinterId(): int
    {
        return $this->evaluate($this->printerId);
    }

    public function getContentType(): string
    {
        return $this->contentType->value;
    }

    public function getContent(): ?string
    {
        return $this->evaluate($this->content);
    }

    public function getTitle(): ?string
    {
        return $this->evaluate($this->title);
    }

    public function getSource(): ?string
    {
        return $this->evaluate($this->source);
    }

    public function getExpireAfter(): int
    {
        return $this->evaluate($this->expireAfter);
    }

    public function getQuantity(): int
    {
        return $this->evaluate($this->quantity);
    }

    public static function getDefaultName(): ?string
    {
        return 'create_print_job_table_action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Print');

        $this->icon('heroicon-s-printer');

        $this->color('primary');

        $this->iconButton();

        $this->successNotificationTitle('Print job request has been sent successfully.');

        $this->action(function (): void {   
            
            $printNode = new PrintNode(env('PRINTNODE_API_KEY'));

            $request = new PrintJobs\PostPrintJob;

            $request->body()->merge([
                'printerId' => $this->getPrinterId(),
                'contentType' => $this->getContentType(),
                'content' => $this->getContent(),
                'title' => $this->getTitle() ?: Str::uuid(),
                'source' => $this->getSource() ?: '-',
                'options' => null,
                'expirAfter' => $this->getExpireAfter(),
                'qty' => $this->getQuantity(), 
            ]);

            $response = $printNode->send($request);
                            
            if (! $response->created()) {
                
                Notification::make()
                    ->danger()
                    ->title($response->json()['code'])
                    ->body($response->json()['message'])
                    ->persistent()
                    ->send();

                return;
            }

            $this->success();
        });
    }
}
