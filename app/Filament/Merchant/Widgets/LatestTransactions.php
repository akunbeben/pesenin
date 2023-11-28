<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Transaction;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;

class LatestTransactions extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 4;

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
