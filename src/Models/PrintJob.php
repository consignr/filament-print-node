<?php

namespace Consignr\FilamentPrintNode\Models;

use Sushi\Sushi;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Consignr\FilamentPrintNode\Enums\PrintJobState;

class PrintJob extends Model
{
    use Sushi;

    protected $casts = [
        'state' => PrintJobState::class,
        'printer' => 'array',
        'computer' => 'array'
    ];

    /**
     * Get the printer_id.
     */
    protected function printerId(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->printer['id'],
        );
    }

    /**
     * Get the printer_name.
     */
    protected function printerName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->printer['name'],
        );
    }

    /**
     * Get the printer_desc.
     */
    protected function printerDesc(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->printer['description'],
        );
    }   
    
    

    /**
     * Get the computer_name.
     */
    protected function computerName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->computer['name'],
        );
    }    

    /**
     * Model Rows
     *
     * @return void
     */
    public function getRows()
    {
        //API
        $jobs = Http::withBasicAuth(env('PRINTNODE_API_KEY'), env('PRINTNODE_PASSWORD'))
                    ->get('https://api.printnode.com/printjobs')->json();
       
        //filtering some attributes
        $jobs = Arr::map($jobs, function ($item) {
            $printer = Arr::pull($item, 'printer');
            $computer = Arr::pull($printer, 'computer');

            $item['printer'] = json_encode($printer);
            $item['computer'] = json_encode($computer);
            
            return Arr::only($item,
                [
                    'id',
                    'title',
                    'contentType',
                    'source',
                    'expireAt',
                    'createTimestamp',
                    'state',
                    'printer',
                    'computer'
                ]
            );
        });

        return $jobs;
    }
}
