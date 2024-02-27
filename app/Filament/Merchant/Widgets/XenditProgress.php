<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Merchant;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class XenditProgress extends Widget
{
    protected static string $view = 'filament.merchant.widgets.xendit-progress';

    public function getColumnSpan(): int | string | array
    {
        return 6;
    }

    public function checkProgress()
    {
        if (!Filament::getTenant()->xendit_in_progress) {
            $this->redirect(Filament::getUrl(Filament::getTenant()));
        }
    }
}
