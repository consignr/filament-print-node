<?php

namespace Consignr\FilamentPrintNode\Models;

use Sushi\Sushi;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Consignr\FilamentPrintNode\Api\PrintNode;
use Consignr\FilamentPrintNode\Api\Requests\PrintJobs;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Consignr\FilamentPrintNode\Enums\PrintJobState;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        $printNode = new PrintNode(env('PRINTNODE_API_KEY'));

        $response = $printNode->send(new PrintJobs\GetPrintJobs); 

        if ($response->ok()) {
       
            //filtering some attributes
            $jobs = $response->collect()->map(function ($item) {
                $printer = Arr::pull($item, 'printer');
                $computer = Arr::pull($printer, 'computer');

                $item['printer'] = json_encode($printer);
                $item['printer_id'] = $printer['id'];
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
                        'printer_id',
                        'computer'
                    ]
                );
            })
            ->toArray();

            return $jobs;
        }
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(\Consignr\FilamentPrintNode\Models\Printer::class);
    }
}
