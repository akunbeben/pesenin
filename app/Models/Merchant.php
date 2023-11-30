<?php

namespace App\Models;

use Filament\Facades\Filament;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel\Concerns\HasAvatars;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable;
use Spatie\Color\Rgb;

class Merchant extends Model implements \OwenIt\Auditing\Contracts\Auditable, HasAvatar
{
    use Auditable;
    use HasAvatars;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'cloudflare_email',
        'name',
        'address',
        'phone',
        'business_id',
        'webhook_token',
        'city',
        'country',
        'zip',
    ];

    protected $casts = [
        'webhook_token' => 'encrypted',
    ];

    protected $auditExclude = [
        'id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Merchant $merchant) {
            $merchant->uuid = Str::orderedUuid();
        });

        static::created(fn (Merchant $merchant) => $merchant->setting()->create());
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class);
    }

    public function fullAddress(): Attribute
    {
        return Attribute::get(function () {
            return "{$this->address}, {$this->city}, {$this->country}, {$this->zip}";
        });
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $name = str(Filament::getNameForDefaultAvatar($this))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        $backgroundColor = Rgb::fromString('rgb(' . FilamentColor::getColors()['gray'][800] . ')')->toHex();

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=' . str($backgroundColor)->after('#');
    }
}
