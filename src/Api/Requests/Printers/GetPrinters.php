<?php

namespace Consignr\FilamentPrintNode\Api\Requests\Printers;

use Saloon\Enums\Method;

class GetPrinters extends BasePrintersRequest
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;
}
