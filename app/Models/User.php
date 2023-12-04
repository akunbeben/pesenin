<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPassword as ResetPasswordNotification;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Filament\Panel\Concerns\HasAvatars;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Color\Rgb;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasDefaultTenant, HasMedia, HasTenants
{
    use HasApiTokens;
    use HasAvatars;
    use HasFactory;
    use InteractsWithMedia;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'require_reset',
        'active_merchant',
        'employee_of',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'require_reset' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->uuid = Str::orderedUuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->activeMerchant;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->merchants->contains($tenant);
    }

    public function getTenants(Panel $panel): array | Collection
    {
        return $this->merchants;
    }

    public function merchants(): HasMany
    {
        return $this->hasMany(Merchant::class);
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'employee_of');
    }

    public function activeMerchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'active_merchant');
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() !== 'central') {
            return true;
        }

        return in_array($this->email, explode(',', config('app.central')));
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
