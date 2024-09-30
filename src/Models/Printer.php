<?php

namespace Consignr\FilamentPrintNode\Models;

use Sushi\Sushi;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use Sushi;
    
    /**
     * Model Rows
     *
     * @return void
     */
    public function getRows()
    {
        //API
        $printers = Http::withBasicAuth(env('PRINTNODE_API_KEY'), env('PRINTNODE_PASSWORD'))
                         ->get('https://api.printnode.com/printers')->json();

        //filtering some attributes
        $printers = Arr::map($printers, function ($item) {
            return Arr::only($item,
                [
                    "id",
                    "name",
                    "description",
                    "default",
                    "createTimestamp",
                    "state"
                ]
            );
        });
 
        return $printers;
    }
}
