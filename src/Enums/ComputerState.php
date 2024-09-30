<?php

namespace Consignr\FilamentPrintNode\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ComputerState: string implements HasLabel, HasIcon, HasColor
{
    case Connected = 'connected';
    case Disconnected = 'disconnected';

    public function getLabel(): string
    {
        return match($this) {
            self::Connected => 'Connected',
            self::Disconnected => 'Not Connected'
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::Connected => 'success',
            self::Disconnected => 'danger'
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Connected => 'heroicon-o-power',
            self::Disconnected => 'heroicon-o-x-circle'
        };
    }
}