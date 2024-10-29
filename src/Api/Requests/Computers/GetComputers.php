<?php

namespace Consignr\FilamentPrintNode\Api\Requests\Computers;

use Saloon\Enums\Method;

class GetComputers extends BaseComputersRequest
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;
}
