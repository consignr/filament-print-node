<?php

namespace Consignr\FilamentPrintNode\Enums;

use Filament\Support\Contracts\HasLabel;

enum RotateOption: int implements HasLabel
{
    case Portrait = 0;
    case Landscape = 90;
    case Portrait180 = 180;
    case Landscape180 = 270;


    public function getLabel(): string
    {
        return match($this) {
            self::Portrait => 'Portrait',
            self::Landscape => 'Orientation',
            self::Portrait180 => 'Portrait 180°',
            self::Landscape180 => 'Landscape 180°',
        };
    }

    public static function toArray(): array
    {
        return [
            self::Portrait->value,
            self::Landscape->value,
            self::Portrait180->value,
            self::Landscape180->value
        ];
    }
}