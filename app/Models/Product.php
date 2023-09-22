<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements \OwenIt\Auditing\Contracts\Auditable, HasMedia
{
    use Auditable;
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'uuid',
        'merchant_id',
        'name',
        'description',
        'price',
        'availability',
        'recommended',
    ];

    protected $casts = [
        'availability' => 'boolean',
        'recommended' => 'boolean',
    ];

    protected $auditExclude = [
        'id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->uuid = Str::orderedUuid();
        });
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner');
    }
}
