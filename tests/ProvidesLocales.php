<?php

namespace App\Tests;

use App\Locales;

trait ProvidesLocales
{
    public static function provideLocales(): array
    {
        return \array_map(static fn ($i) => [$i], Locales::LOCALES);
    }
}
