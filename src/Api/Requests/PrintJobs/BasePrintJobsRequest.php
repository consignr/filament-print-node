<?php

namespace Consignr\FilamentPrintNode\Api\Requests\PrintJobs;

use Saloon\Http\Request;

class BasePrintJobsRequest extends Request
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/printjobs';
    }
}
