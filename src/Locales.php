<?php

namespace App;

final class Locales
{
    public const string REGEX_PART = '(?:en|fr)';
    public const string REGEX = '^(?:'.self::REGEX_PART.')(?:[_-][a-z]{2})?$';
    public const array LOCALES = [
        'en',
        'fr',
    ];
}
