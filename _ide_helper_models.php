<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */

namespace App\Models{
    /**
     * App\Models\Merchant
     *
     * @property int $id
     * @property int $user_id
     * @property string $uuid
     * @property string $name
     * @property string $address
     * @property string $city
     * @property string $country
     * @property string $zip
     * @property string $phone
     * @property string|null $lmsqueezy_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
     * @property-read int|null $audits_count
     * @property-read \LemonSqueezy\Laravel\Customer|null $customer
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
     * @property-read int|null $products_count
     * @property-read \App\Models\Setting|null $setting
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \LemonSqueezy\Laravel\Subscription> $subscriptions
     * @property-read int|null $subscriptions_count
     * @property-read \App\Models\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant query()
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereAddress($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCity($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCountry($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereLmsqueezyId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant wherePhone($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUuid($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereZip($value)
     */
    class Merchant extends \Eloquent implements \Filament\Models\Contracts\HasAvatar, \OwenIt\Auditing\Contracts\Auditable
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Product
     *
     * @property int $id
     * @property string $uuid
     * @property int $merchant_id
     * @property string $name
     * @property string|null $description
     * @property string $price
     * @property bool $availability
     * @property bool $recommended
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
     * @property-read int|null $audits_count
     * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
     * @property-read int|null $media_count
     * @property-read \App\Models\Merchant $merchant
     *
     * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Product query()
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereAvailability($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereMerchantId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product wherePrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereRecommended($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereUuid($value)
     */
    class Product extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable, \Spatie\MediaLibrary\HasMedia
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Setting
     *
     * @property int $id
     * @property int $merchant_id
     * @property bool $cash_mode
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
     * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCashMode($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Setting whereMerchantId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
     */
    class Setting extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * App\Models\User
     *
     * @property int $id
     * @property string $uuid
     * @property string $name
     * @property string $email
     * @property \Illuminate\Support\Carbon|null $email_verified_at
     * @property mixed $password
     * @property string|null $remember_token
     * @property bool $require_reset
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Merchant> $merchants
     * @property-read int|null $merchants_count
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
     * @property-read int|null $notifications_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
     * @property-read int|null $tokens_count
     *
     * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User query()
     * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereRequireReset($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereUuid($value)
     */
    class User extends \Eloquent implements \Filament\Models\Contracts\HasAvatar, \Filament\Models\Contracts\HasTenants
    {
    }
}
