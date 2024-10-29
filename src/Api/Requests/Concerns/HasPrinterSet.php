<?php

namespace Consignr\FilamentPrintNode\Api\Requests\Concerns;

trait HasPrinterSet 
{
    public function getTransformedPrinterSet(): ?string
    {
        return '/' . implode(',', $this->printerSet);
    }
}