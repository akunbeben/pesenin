<?php

namespace App\Traits\Orders;

enum Serving: int
{
    case NotReady = 1;
    case Waiting = 2;
    case Processed = 3;
    case Completed = 4;
    case Finished = 5;

    public function color(): string
    {
        return match ($this) {
            self::NotReady => 'danger',
            self::Waiting => 'warning',
            self::Processed => 'primary',
            self::Completed => 'success',
        };
    }

    public function next(): self
    {
        return match ($this) {
            self::NotReady => self::Waiting,
            self::Waiting => self::Processed,
            self::Processed => self::Completed,
            self::Completed => self::Finished,
        };
    }

    public function prev(): self
    {
        return match ($this) {
            self::Finished => self::Completed,
            self::Completed => self::Processed,
            self::Processed => self::Waiting,
            self::Waiting => self::NotReady,
        };
    }
}
