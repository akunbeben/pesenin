<?php

namespace App\Support;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class DevelopmentUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        return asset("storage/{$this->getPathRelativeToRoot()}");
    }
}
