<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'agent',
        'ip',
        'fingerprint',
        'finished',
    ];

    protected $casts = ['finished' => 'boolean'];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }
}
