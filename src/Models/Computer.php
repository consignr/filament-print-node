<?php

namespace Consignr\FilamentPrintNode\Models;

use Sushi\Sushi;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Consignr\FilamentPrintNode\Enums\ComputerState;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        //API
        $computers = Http::withBasicAuth(env('PRINTNODE_API_KEY'), env('PRINTNODE_PASSWORD'))
                         ->get('https://api.printnode.com/computers')->json();

        //filtering some attributes
        $computers = Arr::map($computers, function ($item) {
            return Arr::only($item,
                [
                    "id",
                    "name",
                    "inet",
                    "inet6",
                    "hostname",
                    "version",
                    "jre",
                    "createTimestamp",
                    "state"
                ]
            );
        });
 
        return $computers;
    }

    public function printers(): HasMany
    {
        return $this->hasMany(\Consignr\FilamentPrintNode\Models\Printer::class);
    }
}
