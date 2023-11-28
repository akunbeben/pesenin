<?php

namespace App\Filament\Merchant\Pages;

use Filament\Pages\Page;

class OrderInQueues extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-queue-list';

    protected static ?string $navigationGroup = 'Active orders';

    protected static string $view = 'filament.merchant.pages.order-in-queues';
}
