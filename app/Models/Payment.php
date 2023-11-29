<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'event',
        'business_id',
        'data',
    ];

    protected $casts = [
        'data' => 'object',
    ];
}
