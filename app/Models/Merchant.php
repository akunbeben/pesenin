<?php

namespace App\Models;

use Filament\Facades\Filament;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel\Concerns\HasAvatars;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use LemonSqueezy\Laravel\Billable;
use Spatie\Color\Rgb;

class Merchant extends Model implements HasAvatar
{
    use Billable;
    use HasAvatars;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'name',
        'address',
        'phone',
    ];

    protected static function booted(): void
    {
        static::creating(function (Merchant $merchant) {
            $merchant->uuid = Str::orderedUuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $name = str(Filament::getNameForDefaultAvatar($this))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        $backgroundColor = Rgb::fromString('rgb(' . FilamentColor::getColors()['gray'][950] . ')')->toHex();

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=' . str($backgroundColor)->after('#');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
