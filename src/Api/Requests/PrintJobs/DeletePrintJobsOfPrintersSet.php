<?php

namespace Consignr\FilamentPrintNode\Api\Requests\PrintJobs;

use Saloon\Enums\Method;
use Consignr\FilamentPrintNode\Api\Requests\Concerns\HasPrinterSet;
use Consignr\FilamentPrintNode\Api\Requests\Printers\BasePrintersRequest;

class DeletePrintJobsOfPrintersSet extends BasePrintersRequest
{
    use HasPrinterSet;

    public function __construct(
        protected readonly array $printerSet
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
        return parent::resolveEndpoint() . $this->getTransformedPrinterSet() . '/printjobs';
    }
}
