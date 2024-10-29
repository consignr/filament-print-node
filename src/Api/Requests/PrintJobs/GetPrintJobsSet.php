<?php

namespace Consignr\FilamentPrintNode\Api\Requests\PrintJobs;

use Saloon\Enums\Method;
use Consignr\FilamentPrintNode\Api\Requests\Concerns\HasPrintJobSet;

class GetPrintJobsSet extends BasePrintJobsRequest
{
    use HasPrintJobSet;

    public function __construct(
        protected readonly array $printJobSet
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
        return parent::resolveEndpoint() . $this->getTransformedPrintJobSet();
    }
}
