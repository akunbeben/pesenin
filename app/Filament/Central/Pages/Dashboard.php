<?php

namespace App\Filament\Central\Pages;

use BezhanSalleh\FilamentGoogleAnalytics\Widgets;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-home';

    protected static string $view = 'filament.central.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\PageViewsWidget::class,
            Widgets\VisitorsWidget::class,
            Widgets\ActiveUsersOneDayWidget::class,
            Widgets\ActiveUsersSevenDayWidget::class,
            Widgets\ActiveUsersTwentyEightDayWidget::class,
            Widgets\SessionsWidget::class,
            Widgets\SessionsDurationWidget::class,
            Widgets\SessionsByCountryWidget::class,
            Widgets\SessionsByDeviceWidget::class,
            Widgets\MostVisitedPagesWidget::class,
            Widgets\TopReferrersListWidget::class,
        ];
    }
}
