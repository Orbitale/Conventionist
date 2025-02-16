<?php

namespace App;

final class Locales
{
    public const string REGEX = '^(?:en|fr)(?:[_-][a-z]{2})?$';

    public static function getList(): array
    {
        return [
            'en',
            'fr',
        ];
    }
}
