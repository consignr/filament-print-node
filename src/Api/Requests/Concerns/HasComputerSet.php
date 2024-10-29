<?php

namespace Consignr\FilamentPrintNode\Api\Requests\Concerns;

trait HasComputerSet 
{
    public function getTransformedComputerSet(): ?string
    {
        return '/' . implode(',', $this->computerSet);
    }
}