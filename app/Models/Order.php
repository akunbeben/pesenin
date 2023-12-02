<?php

namespace App\Models;

use App\Traits\Orders\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_id',
        'number',
        'total',
        'additional',
        'status',
    ];

    protected $casts = [
        'status' => Status::class,
        'total' => 'integer',
        'additional' => 'object',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Order $model) {
            $model->number = 'ref-' . now()->format('ymd') . '-' . uniqid();
        });
    }

    public function getRouteKeyName()
    {
        return 'number';
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
