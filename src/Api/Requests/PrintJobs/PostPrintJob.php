<?php

namespace Consignr\FilamentPrintNode\Api\Requests\PrintJobs;

use Saloon\Enums\Method;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class PostPrintJob extends BasePrintJobsRequest implements HasBody
{
    use HasJsonBody;
    
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;
}
