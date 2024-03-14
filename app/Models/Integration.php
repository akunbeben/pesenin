<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Integration extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'client_id',
        'client_secret',
        'access_token',
        'token_expiration',
    ];

    protected $casts = [
        'client_id' => 'encrypted',
        'client_secret' => 'encrypted',
        'access_token' => 'encrypted',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function tokenValid(): Attribute
    {
        return Attribute::get(
            fn () => $this->created_at
                ->addSeconds($this->token_expiration)
                ->isFuture()
        );
    }
}
