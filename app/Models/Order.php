<?php

namespace App\Models;

use App\Traits\Orders\Serving;
use App\Traits\Orders\Status;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_id',
        'number',
        'total',
        'additional',
        'status',
        'serving',
        'queued_at',
    ];

    protected $casts = [
        'status' => Status::class,
        'serving' => Serving::class,
        'total' => 'integer',
        'additional' => 'collection',
        'queued_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Order $model) {
            $model->number = 'ref-' . now()->format('ymd') . '-' . uniqid();
        });

        static::addGlobalScope('owned', function (Builder $builder) {
            $builder->when(
                Filament::getTenant(),
                fn (Builder $builder) => $builder->whereRelation('table', function (Builder $query) {
                    $query->whereBelongsTo(Filament::getTenant());
                })
            )->when(
                auth()->user()->employee_of,
                fn (Builder $builder) => $builder->whereRelation('table', function (Builder $query) {
                    $query->where('merchant_id', auth()->user()->employee_of);
                })
            );
        });
    }

    public function getRouteKeyName()
    {
        return 'number';
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function table(): HasOneThrough
    {
        return $this->hasOneThrough(Table::class, Scan::class, 'id', 'id', 'scan_id', 'table_id');
    }

    public function scopePaid(Builder $query): void
    {
        $query->where('status', Status::Success);
    }

    public function scopeToday(Builder $query): void
    {
        $query->whereRelation('payment', fn ($subQuery) => $subQuery->whereBetween(
            'updated_at',
            [now()->startOfDay(), now()->endOfDay()]
        ));
    }

    public function scopeThisMonth(Builder $query): void
    {
        $query->whereRelation('payment', fn ($subQuery) => $subQuery->whereBetween(
            'updated_at',
            [now()->startOfMonth(), now()->endOfMonth()]
        ));
    }

    public function cancel(): void
    {
        DB::beginTransaction();

        try {
            $this->update(['status' => Status::Canceled, 'serving' => Serving::Canceled]);
            $this->payment->update(['status' => Status::Canceled]);
        } catch (\Throwable $th) {
            DB::rollBack();

            logger()->error($th->getMessage());

            Notification::make()
                ->title(__('Order cancelation failed, please try again.'))
                ->danger()
                ->send();

            return;
        }

        DB::commit();
    }

    public function process(bool $forward = true): bool
    {
        return $this->update([
            'serving' => $forward ? $this->serving->next() : $this->serving->prev(),
            'queued_at' => now(),
        ]);
    }
}
