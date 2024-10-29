<?php

namespace Consignr\FilamentPrintNode\Api\Requests\PrintJobs;

use Saloon\Enums\Method;
use Consignr\FilamentPrintNode\Api\Requests\Concerns\HasPrintJobSet;

class GetPrintJobsStates extends BasePrintJobsRequest
{
    use HasPrintJobSet;

    public function __construct(
        protected readonly ?array $printJobSet = null
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
        return parent::resolveEndpoint() . ($this->printJobSet ? $this->getTransformedPrintJobSet() : null) . '/states';
    }
}
