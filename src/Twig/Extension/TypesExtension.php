<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

final class TypesExtension extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            new TwigTest('numeric', \is_numeric(...)),
        ];
    }
}
