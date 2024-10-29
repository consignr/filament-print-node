<?php

namespace Consignr\FilamentPrintNode\Api\Requests\Computers;

use Saloon\Http\Request;

class BaseComputersRequest extends Request
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/computers';
    }
}
