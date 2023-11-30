<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Transaction;
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
            ->query(
                Transaction::query()->latest('created'),
            )
            ->columns([
                Tables\Columns\TextColumn::make('reference_id')
                    ->label(__('Ref')),
                Tables\Columns\TextColumn::make('channel_code')
                    ->formatStateUsing(fn (Transaction $record) => last(explode('_', $record->channel_code)))
                    ->label(__('Payment method')),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn (string $state) => Number::currency($state, 'IDR', config('app.locale')))
                    ->label(__('Total')),
                Tables\Columns\TextColumn::make('settlement_status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => str($state)->title()->toString())
                    ->color(fn (string $state) => match ($state) {
                        'PENDING' => 'warning',
                        'SETTLED' => 'success',
                        default => 'gray',
                    })
                    ->label(__('Settlement')),
                Tables\Columns\TextColumn::make('estimated_settlement_time')
                    ->dateTime()
                    ->label(__('Settlement time')),
            ]);
    }
}
