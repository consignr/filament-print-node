<?php

namespace Consignr\FilamentPrintNode\Api\Requests\PrintJobs;

use Saloon\Enums\Method;

class GetPrintJobs extends BasePrintJobsRequest
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;
}
