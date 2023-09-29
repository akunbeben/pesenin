<?php

namespace App\Data;

use App\Models\Product;
use Livewire\Wireable;

class CartProduct implements Wireable
{
    public function __construct(
        protected Product $product,
        protected ?string $variant = null,
        protected int $amount = 1,
    ) {
        //
    }

    public function toLivewire(): array
    {
        return [
            'product' => $this->product,
            'variant' => $this->variant,
            'amount' => $this->amount,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['product'], $value['variant'], $value['amount']);
    }
}
