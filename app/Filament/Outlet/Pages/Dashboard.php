<?php

namespace App\Filament\Outlet\Pages;

use Filament\Pages\Dashboard as Page;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-home';

    public static function getNavigationLabel(): string
    {
        return __('Dashboard');
    }

    public function getTitle(): string | Htmlable
    {
        return __('Dashboard');
    }
}
