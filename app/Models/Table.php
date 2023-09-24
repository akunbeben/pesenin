<?php

namespace App\Models;

use App\Traits\Tables\QRStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Table extends Model
{
    use HasFactory;
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

    public function name(): Attribute
    {
        return Attribute::get(fn () => "{$this->prefix}{$this->number}{$this->suffix}");
    }
}
