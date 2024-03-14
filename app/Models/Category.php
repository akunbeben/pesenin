<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Sqids\Sqids;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
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
        $hasher = new Sqids(minLength: 3);

        return Attribute::get(fn () => $hasher->encode([$this->getKey()]));
    }

    public function hash(string $salt): string
    {
        return (new Sqids($salt, minLength: 5))->encode([$this->getKey()]);
    }

    public function reverse(?string $hashed, ?string $salt): bool
    {
        if (! $hashed) {
            return false;
        }

        return $this->getKey() === Arr::first((new Sqids($salt, minLength: 5))->decode($hashed));
    }
}
