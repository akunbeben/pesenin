<?php

namespace App\Filament\Merchant\Pages;

use Filament\Pages\Dashboard;
use Illuminate\Contracts\Support\Htmlable;

class BaseDashboard extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-m-home';

    protected static ?string $navigationGroup = 'Front of House';

    public static function getNavigationLabel(): string
    {
        return __('Dashboard');
    }

    public function getTitle(): string | Htmlable
    {
        return __('Dashboard');
    }
}
