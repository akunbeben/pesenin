<?php

namespace App\Traits\Orders;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum PaymentChannels: int implements HasDescription, HasLabel
{
    case QRIS = 1;
    case EWALLET = 2;
    case CASH = 3;

    public function id(): ?string
    {
        return strtolower($this->name);
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::QRIS => 'QRIS',
            self::EWALLET => 'E-Wallet',
            self::CASH => 'Cash',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::QRIS => __('QRIS will charge for payment gateway fee 0.7%'),
            self::EWALLET => __('DANA, OVO, ShopeePay, JeniusPay, LinkAja, AstraPay will charge for payment gateway fee 4%'),
            self::CASH => __('There is no payment gateway fee for cash payment.'),
        };
    }

    public function getLogo(): string
    {
        return match ($this) {
            self::QRIS => asset('images/QRIS.png'),
            self::EWALLET => asset('images/ewallet.png'),
            self::CASH => asset('images/cash.png'),
        };
    }
}