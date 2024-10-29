<?php

namespace Consignr\FilamentPrintNode\Api\Requests\PrintJobs;

use Saloon\Enums\Method;

class DeletePrintJobs extends BasePrintJobsRequest
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::DELETE;
}
