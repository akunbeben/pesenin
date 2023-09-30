<?php

namespace App\Models;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'name',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function hashed(): Attribute
    {
        $hasher = new Hashids(config('app.key'), 3);

        return Attribute::get(fn () => $hasher->encode($this->getKey()));
    }

    public function reverse(?string $hashed): bool
    {
        if (! $hashed) {
            return false;
        }

        return $this->getKey() === (new Hashids(config('app.key'), 3))->decode($hashed)[0];
    }
}
