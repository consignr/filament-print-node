<?php

namespace Consignr\FilamentPrintNode\Api\Requests\Printers;

use Saloon\Enums\Method;
use Consignr\FilamentPrintNode\Api\Requests\Concerns\HasComputerSet;
use Consignr\FilamentPrintNode\Api\Requests\Computers\BaseComputersRequest;

class GetPrintersOfComputersSet extends BaseComputersRequest
{
    use HasComputerSet;

    public function __construct(
        protected readonly array $computerSet
    ) {}

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return parent::resolveEndpoint() . $this->getTransformedComputerSet() . '/printers';
    }
}
