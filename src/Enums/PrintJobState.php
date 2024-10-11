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
    case Queued = 'queued';
    case InProgress = 'in_progress';

    public function getLabel(): string
    {
        return match($this) {
            self::New => 'New',
            self::SentToClient => 'Sent to Client',
            self::Done => 'Done',
            self::Error => 'Error',
            self::Expired => 'Expired',
            self::Queued => 'Queued',
            self::InProgress => 'In Progress'
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::New => 'info',
            self::SentToClient => 'info',
            self::Done => 'success',
            self::Error => 'danger',
            self::Expired => 'warning',
            self::Queued => 'default',
            self::InProgress => 'primary'
        };
    }
}