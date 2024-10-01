<?php

namespace Consignr\FilamentPrintNode\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PrinterState: string implements HasLabel, HasIcon, HasColor
{
    case Online = 'online';
    case Offline = 'offline';

    public function getLabel(): string
    {
        return match($this) {
            self::Online => 'Online',
            self::Offline => 'Offline'
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::Online => 'success',
            self::Offline => 'danger'
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Online => 'heroicon-o-signal',
            self::Offline => 'heroicon-o-x-signal-slash'
        };
    }
}