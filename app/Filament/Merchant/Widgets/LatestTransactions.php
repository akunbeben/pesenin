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
        return ! Feature::for(Filament::getTenant())->active('feature_payment') ? [
            'default' => 6,
            'sm' => 6,
        ] : [
            'default' => 'full',
            'sm' => 6,
            'md' => 6,
            'lg' => 6,
            'xl' => 6,
            '2xl' => 6,
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
            ->headerActions([
                Tables\Actions\Action::make('More detail')
                    ->color('gray')
                    ->icon('heroicon-m-link')
                    ->translateLabel()
                    ->url(route('filament.merchant.resources.payments.index', [Filament::getTenant()])),
            ])
            ->recordUrl(fn (Payment $record) => route('filament.merchant.resources.payments.view', [Filament::getTenant(), $record]))
            ->query(
                Payment::query()
                    ->whereBelongsTo(Filament::getTenant(), 'merchant')
                    ->latest('data->paid_at')
                    ->latest('data->status')
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('reference_id')
                    ->getStateUsing(fn (Payment $record) => $record->data?->external_id)
                    ->label(__('Ref')),
                Tables\Columns\TextColumn::make('payment_channel')
                    ->getStateUsing(fn (Payment $record) => $record->data?->payment_channel)
                    ->label(__('Via')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->getStateUsing(fn (Payment $record) => $record->loadMissing('order')->status)
                    ->label(__('Status')),
                Tables\Columns\TextColumn::make('amount')
                    ->getStateUsing(fn (Payment $record) => Number::currency($record->data?->paid_amount, 'IDR', config('app.locale')))
                    ->label(__('Total')),
                Tables\Columns\TextColumn::make('paid_at')
                    ->getStateUsing(fn (Payment $record) => $record->data?->paid_at)
                    ->dateTime()
                    ->label(__('Paid at')),
            ]);
    }
}
