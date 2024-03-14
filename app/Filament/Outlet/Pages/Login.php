<?php

namespace App\Filament\Outlet\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Auth\Login as Page;

class Login extends Page
{
    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill(app()->isLocal() ? [
            'email' => 'joni.doe@example.com',
            'password' => 'password',
            'remember' => true,
        ] : []);
    }
}
