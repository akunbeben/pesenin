<?php

namespace App\Traits\Orders;

enum Status: int
{
    case Pending = 1;
    case Processed = 2;
    case Success = 3;

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processed => 'primary',
            self::Success => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-m-exclamation-circle',
            self::Processed => 'heroicon-m-arrow-path',
            self::Success => 'heroicon-m-check-circle',
        };
    }
}
