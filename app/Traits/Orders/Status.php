<?php

namespace App\Traits\Orders;

enum Status: int
{
    case Pending = 1;
    case Processed = 2;

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Processed => 'primary',
        };
    }
}
