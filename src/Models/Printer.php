<?php

namespace Consignr\FilamentPrintNode\Models;

use Sushi\Sushi;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Consignr\FilamentPrintNode\Api\PrintNode;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Consignr\FilamentPrintNode\Enums\PrinterState;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Consignr\FilamentPrintNode\Api\Requests\Printers;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;

class Printer extends Model
{
    use Sushi;

    protected $casts = [
        'state' => PrinterState::class,
        'capabilities' => 'array',
        'createTimestamp' => 'datetime'
    ];

    /**
     * Get the has_bins attribute.
     */
    protected function hasBins(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => filled($this->capabilities['bins']),
        );
    }

    /**
     * Get the has_dpis attribute.
     */
    protected function hasDpis(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => filled($this->capabilities['dpis']),
        );
    }

    /**
     * Get the has_medias attribute.
     */
    protected function hasMedias(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => filled($this->capabilities['medias']),
        );
    }

    /**
     * Get the has_papers attribute.
     */
    protected function hasPapers(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => filled(Arr::whereNotNull($this->capabilities['papers'])),
        );
    }

    /**
     * Get the capabilities.
     */
    protected function capabilities(): Attribute
    {
        return Attribute::make(
            get: fn ($value): array => json_decode($value, true),
        );
    }

    /**
     * Get the tray_names.
     */
    protected function trayNames(): Attribute
    {
        return Attribute::make(
            get: fn (): array => $this->capabilities['bins'],
        );
    }

    /**
     * Get the collate.
     */
    protected function collate(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->capabilities['collate'],
        );
    }

    /**
     * Get the color.
     */
    protected function color(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->capabilities['color'],
        );
    }

    /**
     * Get the copies.
     */
    protected function copies(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->capabilities['copies'],
        );
    }

    /**
     * Get the supported_dpis.
     */
    protected function supportedDpis(): Attribute
    {
        return Attribute::make(
            get: fn (): array => $this->capabilities['dpis'],
        );
    }

    /**
     * Get the duplex.
     */
    protected function duplex(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->capabilities['duplex'],
        );
    }

    /**
     * Get the media_names.
     */
    protected function mediaNames(): Attribute
    {
        return Attribute::make(
            get: fn (): array => $this->capabilities['medias'],
        );
    }

    /**
     * Get the n_up_printing.
     */
    protected function NUpPrinting(): Attribute
    {
        return Attribute::make(
            get: fn (): array => $this->capabilities['nup'],
        );
    }

    /**
     * Get the minimum_supported_width.
     */
    protected function minimumSupportedWidth(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->capabilities['extent'][0][0],
        );
    }

    /**
     * Get the minimum_supported_height.
     */
    protected function minimumSupportedHeight(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->capabilities['extent'][0][1],
        );
    }

    /**
     * Get the maximum_supported_width.
     */
    protected function maximumSupportedWidth(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->capabilities['extent'][1][0],
        );
    }

    /**
     * Get the maximum_supported_height.
     */
    protected function maximumSupportedHeight(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->capabilities['extent'][1][1],
        );
    }

    /**
     * Get the papers.
     */
    protected function papers(): Attribute
    {
        return Attribute::make(
            get: fn (): array => collect($this->capabilities['papers'])
                ->map(function ($item) {
                    $keys = ['width', 'height'];
                    return array_combine($keys, $item);
                })
                ->toArray(),
        );
    }

    /**
     * Get the printrate.
     */
    protected function printrate(): Attribute
    {
        return Attribute::make(
            get: fn (): ?array => $this->capabilities['printrate'],
        );
    }

    /**
     * Get the supports_custom_paper_size.
     */
    protected function supportsCustomPaperSize(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->capabilities['supports_custom_paper_size'],
        );
    }
    
    /**
     * Model Rows
     *
     * @return void
     */
    public function getRows()
    {
        $printNode = new PrintNode(env('PRINTNODE_API_KEY'));

        $response = $printNode->send(new Printers\GetPrinters); 

        if ($response->ok()) {
                    
            //filtering some attributes
            $printers = $response->collect()->map(function ($item) {
                $computer = Arr::pull($item, 'computer');
                $item['computer_id'] = $computer['id'];
                return collect($item)->map(function ($i, $k) {
                    
                    if ($k === 'capabilities') {
                    return json_encode($i);
                    }

                    return $i;
                })->only([
                    "id",
                    "name",
                    "description",
                    "default",
                    "createTimestamp",
                    "state",
                    "capabilities",
                    'computer_id'
                ]);
            })->toArray();

            return $printers;
        }
    }

    public function printJobs(): HasMany
    {
        return $this->hasMany(\Consignr\FilamentPrintNode\Models\PrintJob::class);
    }

    public function computer(): BelongsTo
    {
        return $this->belongsTo(\Consignr\FilamentPrintNode\Models\Computer::class);
    }
}
