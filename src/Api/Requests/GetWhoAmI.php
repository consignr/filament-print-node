<?php

namespace Consignr\FilamentPrintNode\Api\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetWhoAmI extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/whoami';
    }
}
