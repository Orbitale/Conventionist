<?php

namespace App\DataFixtures\Tools;

final readonly class Ref
{
    public function __construct(
        public string $class,
        public string $name,
    ) {
    }
}
