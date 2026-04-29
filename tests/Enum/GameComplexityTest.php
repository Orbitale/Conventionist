<?php

namespace App\Tests\Enum;

use App\Enum\GameComplexity;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class GameComplexityTest extends TestCase
{
    #[TestWith(['light', 'Light'])]
    #[TestWith(['medium_light', 'Medium-light'])]
    #[TestWith(['medium', 'Medium'])]
    #[TestWith(['medium_heavy', 'Medium-heavy'])]
    #[TestWith(['heavy', 'Heavy'])]
    public function testLabel(string $value, string $expected): void
    {
        self::assertSame($expected, GameComplexity::from($value)->getLabel());
    }

    #[TestWith(['light', 'success'])]
    #[TestWith(['medium', 'secondary'])]
    #[TestWith(['heavy', 'danger'])]
    public function testCssClass(string $value, string $expected): void
    {
        self::assertSame($expected, GameComplexity::from($value)->getCssClass());
    }
}
