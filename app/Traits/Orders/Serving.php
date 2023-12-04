<?php

namespace App\Traits\Orders;

enum Serving: int
{
    case NotReady = 1;
    case Waiting = 2;
    case Processed = 3;
    case Completed = 4;

    public function color(): string
    {
        return match ($this) {
            self::NotReady => 'danger',
            self::Waiting => 'warning',
            self::Processed => 'primary',
            self::Completed => 'success',
        };
    }
}
