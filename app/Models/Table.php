<?php

namespace App\Models;

use App\Traits\Tables\QRStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Sqids\Sqids;

class Table extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'prefix',
        'number',
        'suffix',
        'seats',
        'qr_status',
    ];

    protected $casts = [
        'qr_status' => QRStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Table $table) {
            $table->uuid = Str::orderedUuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('qr')->singleFile();
    }

    public function name(): Attribute
    {
        return Attribute::get(fn () => "{$this->prefix}{$this->number}{$this->suffix}");
    }

    public function url(): Attribute
    {
        return Attribute::get(fn () => route(
            'redirector',
            [
                'uid' => $this->uuid,
                'k' => (new Sqids(minLength: 10))->encode([$this->created_at->timestamp]),
            ],
        ));
    }
}
