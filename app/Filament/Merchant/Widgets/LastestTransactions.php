<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Transaction;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;

class LastestTransactions extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string | Htmlable | null
    {
        return __('Lastest transactions');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query(),
            )
            ->columns([
                // ...
            ]);
    }
}
