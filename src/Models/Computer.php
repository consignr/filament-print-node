<?php

namespace Consignr\FilamentPrintNode\Models;

use Sushi\Sushi;
use Illuminate\Database\Eloquent\Model;
use Consignr\FilamentPrintNode\Api\PrintNode;
use Consignr\FilamentPrintNode\Enums\ComputerState;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Consignr\FilamentPrintNode\Api\Requests\Computers;

class Computer extends Model
{
    use Sushi;

    protected $casts = [
        'state' => ComputerState::class,
        'createTimestamp' => 'datetime'
    ];
 
    /**
     * Model Rows
     *
     * @return void
     */
    public function getRows()
    {
        $printNode = new PrintNode(config('filament-print-node.api_key'));

        $response = $printNode->send(new Computers\GetComputers);

        if ($response->ok()) {
            return $response->json();
        }

        return [];        
    }

    public function printers(): HasMany
    {
        return $this->hasMany(\Consignr\FilamentPrintNode\Models\Printer::class);
    }
}
