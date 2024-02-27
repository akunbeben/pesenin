<?php

namespace App\Models;

use App\Traits\Payments\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'business_id',
        'order_id',
        'event',
        'data',
        'priority',
        'note',
        'settlement',
    ];

    protected $casts = [
        'data' => 'object',
        'priority' => 'boolean',
        'status' => Status::class,
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function status(): Attribute
    {
        return Attribute::get(fn () => Status::tryFrom($this->order->status->value));
    }
}
