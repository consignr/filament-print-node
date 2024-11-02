<?php

namespace Consignr\FilamentPrintNode\Enums;

use Filament\Support\Contracts\HasLabel;

enum DuplexOption: string implements HasLabel
{
    case LongEdge = 'long-edge';
    case ShortEdge = 'short-edge';
    case OneSided = 'one-sided';

    public function getLabel(): string
    {
        return match($this) {
            self::LongEdge => 'Flip on Long Edge',
            self::ShortEdge => 'Flip on Short Edge',
            self::OneSided => 'One Sided'
        };
    }

    public static function toArray(): array
    {
        return [
            self::LongEdge->value,
            self::ShortEdge->value,
            self::OneSided->value
        ];
    }
}