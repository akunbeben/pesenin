<?php

namespace App\Traits\Tables;

enum QRStatus: int
{
    case None = 0;
    case Generating = 1;
    case Generated = 2;

    public function color(): string
    {
        return match ($this) {
            self::None => 'gray',
            self::Generating => 'primary',
            self::Generated => 'success',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::None => 'None',
            self::Generating => 'Generating',
            self::Generated => 'Ready to download',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::None => 'heroicon-o-x-circle',
            self::Generating => 'heroicon-o-arrow-path',
            self::Generated => 'heroicon-o-cloud-arrow-down',
        };
    }
}
