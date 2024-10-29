<?php

namespace Consignr\FilamentPrintNode\Api\Requests\Computers;

use Consignr\FilamentPrintNode\Api\Requests\Concerns\HasComputerSet;
use Saloon\Enums\Method;

class DeleteComputersSet extends BaseComputersRequest
{
    use HasComputerSet;

    public function __construct(
        protected readonly array $computerSet
    ) {}

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::DELETE;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return parent::resolveEndpoint() . $this->getTransformedComputerSet();
    }
}
