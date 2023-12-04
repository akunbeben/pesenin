<?php

namespace App\Filament\Outlet\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class OrderInQueues extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-queue-list';

    protected static string $view = 'filament.outlet.pages.order-in-queues';

    public function getHeading(): string | Htmlable
    {
        return __('Order in queues');
    }

    public function getTitle(): string
    {
        return __('Order in queues');
    }

    public static function getNavigationLabel(): string
    {
        return __('Order in queues');
    }
}
