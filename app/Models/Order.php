<?php

namespace App\Models;

use App\Traits\Orders\Serving;
use App\Traits\Orders\Status;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
