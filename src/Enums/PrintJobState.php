<?php

namespace Consignr\FilamentPrintNode\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PrintJobState: string implements HasLabel, HasColor
{
    case New = 'new';
    case SentToClient = 'sent_to_client';
    case Done = 'done';
    case Error = 'error';
    case Expired = 'expired';

    public function getLabel(): string
    {
        return match($this) {
            self::New => 'New',
            self::SentToClient => 'Sent to Client',
            self::Done => 'Done',
            self::Error => 'Error',
            self::Expired => 'Expired'
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::New => 'info',
            self::SentToClient => 'default',
            self::Done => 'success',
            self::Error => 'danger',
            self::Expired => 'warning'
        };
    }
}