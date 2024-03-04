<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class CustomLink extends Field
{
    protected string $view = 'forms.components.link';

    protected string $title;

    protected string $url;
}
