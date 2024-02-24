<?php

namespace App\Traits\Orders;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum Status: int implements HasColor, HasIcon
{
    case Pending = 1;
    case Processed = 2;
    case Success = 3;
    case Expired = 4;
    case Manual = 5;

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processed => 'primary',
            self::Success => 'success',
            self::Expired => 'danger',
            self::Manual => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-m-exclamation-circle',
            self::Processed => 'heroicon-m-arrow-path',
            self::Success => 'heroicon-m-check-circle',
            self::Expired => 'heroicon-m-x-circle',
            self::Manual => 'heroicon-m-check-circle',
        };
    }
}
