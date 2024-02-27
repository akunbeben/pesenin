<?php

namespace App\Traits\Payments;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Status: int implements HasColor, HasLabel
{
    case Pending = 1;
    case Processed = 2;
    case Success = 3;
    case Expired = 4;
    case Manual = 5;
    case Canceled = 6;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending, self::Manual => 'PENDING',
            self::Processed => 'PROCESSED',
            self::Success => 'PAID',
            self::Expired => 'EXPIRED',
            self::Canceled => 'CANCELED',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processed => 'primary',
            self::Success => 'success',
            self::Expired, self::Canceled => 'danger',
            self::Manual => 'gray',
        };
    }
}
