<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Transaction;
use Filament\Facades\Filament;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Laravel\Pennant\Feature;

class LatestTransactions extends BaseWidget
{
    protected static ?int $sort = 2;

    public function getColumnSpan(): int | string | array
    {
        return ! Feature::for(Filament::getTenant())->active('ikiosk') ? 'full' : 4;
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
                Transaction::query(),
            )
            ->columns([
                // ...
            ]);
    }
}
