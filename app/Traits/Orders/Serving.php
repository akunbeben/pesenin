<?php

namespace App\Traits\Orders;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Serving: int implements HasColor, HasLabel
{
    case NotReady = 1;
    case Waiting = 2;
    case Processed = 3;
    case Completed = 4;
    case Finished = 5;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NotReady => __('Order is not ready'),
            self::Waiting => __('Waiting in queue'),
            self::Processed => __('Order processed'),
            self::Completed => __('Order completed'),
            self::Finished => __('Order served'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::NotReady => 'danger',
            self::Waiting => 'warning',
            self::Processed => 'primary',
            self::Completed, self::Finished => 'success',
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
