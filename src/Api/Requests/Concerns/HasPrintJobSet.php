<?php

namespace Consignr\FilamentPrintNode\Api\Requests\Concerns;

trait HasPrintJobSet 
{
    public function getTransformedPrintJobSet(): ?string
    {
        return '/' . implode(',', $this->printJobSet);
    }
}