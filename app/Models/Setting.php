<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_mode',
        'ikiosk_mode',
    ];

    protected $casts = [
        'cash_mode' => 'boolean',
        'ikiosk_mode' => 'boolean',
    ];
}
