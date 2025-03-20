<?php

namespace App\Tests\TestUtils;

use App\Locales;

trait ProvidesLocales
{
    /**
     * @return iterable<array{string}>
     */
    public static function provideLocales(): iterable
    {
        foreach (Locales::LOCALES as $locale) {
            yield $locale => [$locale];
        }
    }
}
