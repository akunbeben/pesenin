<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Payment;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Number;
use Laravel\Pennant\Feature;

class LatestTransactions extends BaseWidget
{
    protected static ?int $sort = 2;

    public function getColumnSpan(): int | string | array
    {
        return ! Feature::for(Filament::getTenant())->active('ikiosk') ? 'full' : [
            'default' => 6,
            'sm' => 4,
            'md' => 4,
            'lg' => 4,
            'xl' => 4,
            '2xl' => 4,
        ];
    }

    protected function getTableHeading(): string | Htmlable | null
    {
        return __('Latest transactions');
    }

    protected function getTableDescription(): string | Htmlable | null
    {
        return __('Data will refresh every hour');
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->query(Payment::query()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('reference_id')
                    ->getStateUsing(fn (Payment $record) => $record->data->external_id)
                    ->label(__('Ref')),
                Tables\Columns\TextColumn::make('payment_channel')
                    ->getStateUsing(fn (Payment $record) => $record->data->payment_channel)
                    ->label(__('Payment method')),
                Tables\Columns\TextColumn::make('amount')
                    ->getStateUsing(fn (Payment $record) => Number::currency($record->data->paid_amount, 'IDR', config('app.locale')))
                    ->label(__('Total')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->getStateUsing(fn (Payment $record) => str($record->data->status)->title()->toString())
                    ->color(fn (Payment $record) => match ($record->data->status) {
                        'PENDING' => 'warning',
                        'SETTLED', 'PAID' => 'success',
                        default => 'gray',
                    })
                    ->label(__('Status')),
                Tables\Columns\TextColumn::make('paid_at')
                    ->getStateUsing(fn (Payment $record) => $record->data->paid_at)
                    ->dateTime()
                    ->label(__('Paid at')),
            ]);
    }
}
