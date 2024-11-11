<?php

namespace Consignr\FilamentPrintNode\Models;

use Sushi\Sushi;
use Consignr\FilamentPrintNode\Api;
use Illuminate\Database\Eloquent\Model;
use Consignr\FilamentPrintNode\Enums\OperatingSystem;

class Client extends Model
{
    use Sushi;

    protected $casts = [
        'releaseTimestamp' => 'datetime',
        'os' => OperatingSystem::class
    ];

    /**
     * Model Rows
     *
     * @return array
     */
    public function getRows()
    {
        $printNode = new Api\PrintNode(config('filament-print-node.api_key'));
        
        $response = $printNode->send(new Api\Requests\Clients\GetDownloadClients);

        if ($response->ok()) {

            return $response->json();
        }

        return [];        
    }
}
