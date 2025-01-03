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
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Merchant extends Model implements \OwenIt\Auditing\Contracts\Auditable, HasAvatar, HasMedia
{
    use Auditable;
    use HasAvatars;
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'uuid',
        'cloudflare_email',
        'name',
        'external_id',
        'external_name',
        'address',
        'phone',
        'business_id',
        'webhook_token',
        'city',
        'country',
        'zip',
        'xendit_in_progress',
        'was_paid',
    ];

    protected $hidden = [
        'business_id',
        'webhook_token',
        'cloudflare_email',
        'external_id',
    ];

    protected $casts = [
        'external_id' => 'encrypted',
        'webhook_token' => 'encrypted',
        'xendit_in_progress' => 'boolean',
        'was_paid' => 'boolean',
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

    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'employee_of');
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

    public function integration(): HasOne
    {
        return $this->hasOne(Integration::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'business_id', 'business_id');
    }

    public function fullAddress(): Attribute
    {
        return Attribute::get(function () {
            return "{$this->address}, {$this->city}, {$this->country}, {$this->zip}";
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $name = str(Filament::getNameForDefaultAvatar($this))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        $backgroundColor = Rgb::fromString('rgb(' . FilamentColor::getColors()['gray'][800] . ')')->toHex();

        $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=' . str($backgroundColor)->after('#');

        return $this->getFirstMedia('avatar')?->getUrl() ?? $defaultAvatar;
    }
}
