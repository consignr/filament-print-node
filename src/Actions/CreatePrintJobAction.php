<?php

namespace Consignr\FilamentPrintNode\Actions;

use Closure;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Facades\FilamentIcon;
use Filament\Forms\Components\ToggleButtons;
use Consignr\FilamentPrintNode\Api\PrintNode;
use Consignr\FilamentPrintNode\Models\Printer;
use Consignr\FilamentPrintNode\Enums\ContentType;
use Consignr\FilamentPrintNode\Enums\DuplexOption;
use Consignr\FilamentPrintNode\Enums\RotateOption;
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

    protected array | Closure | null $options = null;

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

    public function options(array | Closure $options): static
    {
        $this->options = $options;

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

    public function getOptions(): ?array
    {
        return $this->evaluate($this->options);
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

        $this->modalSubmitActionLabel('Print');

        $this->successNotificationTitle('Print job request has been sent successfully.');

        $this->action(function (array $data): void { 
            
            $options = $this->getOptions() ?: $data;
            
            $printNode = new PrintNode(env('PRINTNODE_API_KEY'));

            $request = new PrintJobs\PostPrintJob;

            $request->body()->merge([
                'printerId' => $this->getPrinterId(),
                'contentType' => $this->getContentType(),
                'content' => $this->getContent(),
                'title' => $this->getTitle() ?: Str::uuid(),
                'source' => $this->getSource() ?: '-',
                'options' => $options,
                'expirAfter' => $this->getExpireAfter(),
                'qty' => $this->getQuantity(), 
            ]);

            $response = $printNode->send($request);
                            
            if ($response->failed()) {
                
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

        $this->form(function () {
            if ($this->getOptions() != null) {
                return null;
            } else {

                $printer = Printer::find($this->getPrinterId());
                $capabilities = $printer->capabilities;

                $schema = [];

                if ($printer->has_bins) {
                    $schema[] = Select::make('bin')
                        ->options($capabilities['bins'])
                        ->native(false)
                        ->in($capabilities['bins'])
                        ->default(function () use ($capabilities): ?string {
                            if(count($capabilities['bins']) === 1) {
                                return $capabilities['bins'][0];
                            }

                            return null;
                        })
                        ->disabled(fn (): bool => count($capabilities['bins']) === 1);
                }
                if ($printer->collate) {
                    $schema[] = Toggle::make('collate')->default(true);
                }
                if ($printer->copies > 1) {
                    $schema[] = TextInput::make('copies')->numeric()->step(1)->maxValue($printer->copies)->minValue(1)->default(1);
                }
                if ($printer->color) {
                    $schema[] = Select::make('color')->boolean(trueLabel: 'Colour', falseLabel: 'Greyscale')->native(false);
                }
                if ($printer->has_dpis) {
                    $schema[] = Select::make('dpis')
                        ->options($capabilities['dpis'])
                        ->native(false)
                        ->label('DPI')
                        ->in($capabilities['dpis'])
                        ->default(function () use ($capabilities): ?string {
                            if(count($capabilities['dpis']) === 1) {
                                return $capabilities['dpis'][0];
                            }

                            return null;
                        })
                        ->disabled(fn (): bool => count($capabilities['dpis']) === 1);
                }
                if ($printer->duplex) {
                    $schema[] = Select::make('duplex')
                        ->options(DuplexOption::class)
                        ->native(false)
                        ->in(DuplexOption::toArray())
                        ->default(DuplexOption::OneSided->value);
                }
                $schema[] = Toggle::make('fit_to_page');
                if ($printer->has_medias) {
                    $schema[] = Select::make('medias')
                        ->options($capabilities['medias'])
                        ->native(false)
                        ->in($capabilities['medias'])
                        ->default(function () use ($capabilities): ?string {
                            if(count($capabilities['medias']) === 1) {
                                return $capabilities['medias'][0];
                            }

                            return null;
                        })
                        ->disabled(fn (): bool => count($capabilities['medias']) === 1);
                }
                $schema[] = Fieldset::make('Print Range')
                    ->schema([
                        Radio::make('range_option')
                            ->live()
                            ->hiddenLabel()
                            ->default('-')
                            ->options([
                                '-' => 'All',
                                'pages' => 'Pages'
                            ])
                            ->afterStateUpdated(function (Set $set, string $state) {
                                if ($state == '-') {
                                    $set('pages', $state);
                                    $set('range', null);
                                }
                            })
                            ->dehydrated(false),    
                        Group::make([
                            Hidden::make('pages')->default('-'),
                            TextInput::make('range')
                                ->hiddenLabel()
                                ->live(onBlur: true)
                                ->disabled(fn (Get $get): bool => $get('range_option') === '-')
                                ->afterStateUpdated(function (Set $set, ?string $state) {
                                    if ($state) {
                                        $set('pages', str($state)->remove(' ')->toString());
                                    }
                                })
                                ->dehydrated(false),
                            Placeholder::make('helper')
                                ->hiddenLabel()
                                ->content('Enter pages numbers and/or page ranges separated by commas. For example 1,5-12.')
                        ]) 
                    ]);
                if ($printer->hasPapers) {
                    $options = collect($printer->papers)->mapWithKeys(function ($item, $key) {
                        $width = $item['width'] / 10;
                        $height = $item['height'] / 10;
                        
                        return [$key => "{$key} - ({$width}x{$height}mm)"];
                    });
                    
                    $schema[] = Select::make('papers')
                        ->options($options)
                        ->native(false)
                        ->in($options)
                        ->default(function () use ($options): ?string {
                            if(count($options) === 1) {
                                return array_key_first($options->toArray());
                            }

                            return null;
                        })
                        ->disabled(fn (): bool => count($options) === 1);
                }
                $schema[] = Select::make('rotate')
                    ->label('Orientation')
                    ->options(RotateOption::class)
                    ->native(false)
                    ->in(RotateOption::toArray())
                    ->default(RotateOption::Portrait->value);

                return $schema;
            }
        });
        
    }
}
