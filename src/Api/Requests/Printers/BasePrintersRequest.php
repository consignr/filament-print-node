<?php

namespace Consignr\FilamentPrintNode\Api\Requests\Printers;

use Saloon\Http\Request;

class BasePrintersRequest extends Request
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/printers';
    }
}
