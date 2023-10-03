<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'salt',
        'encoded',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}
