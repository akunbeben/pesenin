<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements \OwenIt\Auditing\Contracts\Auditable, HasMedia
{
    use Auditable;
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'uuid',
        'merchant_id',
        'category_id',
        'name',
        'description',
        'price',
        'availability',
        'recommended',
        'variants',
    ];

    protected $casts = [
        'availability' => 'boolean',
        'recommended' => 'boolean',
        'variants' => 'array',
        'price' => 'integer',
    ];

    protected $auditExclude = [
        'id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->uuid = Str::orderedUuid();
        });

        static::addGlobalScope('owned', function (Builder $builder) {
            $builder->when(
                Filament::getTenant(),
                fn (Builder $builder) => $builder->whereBelongsTo(Filament::getTenant())
            )->when(auth()->user()?->employee_of, fn (Builder $builder) => $builder->where(
                'merchant_id',
                auth()->user()->employee_of
            ));
        });
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::class, Item::class, 'product_id', 'id', null, 'order_id');
    }

    public function scopeAvailable(Builder $builder): Builder
    {
        return $builder->where('availability', true);
    }

    public function scopeHighlights(Builder $builder): Builder
    {
        return $builder->where('recommended', true);
    }

    public function scopeSearch(Builder $builder, ?string $keyword): Builder
    {
        return $builder->where('name', 'LIKE', "%{$keyword}%");
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner')->useFallbackUrl(Vite::asset('resources/images/placeholder.png'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->performOnCollections('banner')
            ->nonQueued()
            ->width(200)
            ->height(200);
    }
}
