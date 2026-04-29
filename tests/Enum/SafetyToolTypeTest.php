<?php

namespace App\Tests\Enum;

use App\Enum\SafetyToolType;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class SafetyToolTypeTest extends TestCase
{
    #[TestWith(['x_card', 'X-Card'])]
    #[TestWith(['lines_veils', 'Lines & Veils'])]
    #[TestWith(['open_door', 'Open Door'])]
    #[TestWith(['other', 'Other'])]
    public function testLabel(string $value, string $expected): void
    {
        self::assertSame($expected, SafetyToolType::from($value)->getLabel());
    }

    #[TestWith(['x_card', 'danger'])]
    #[TestWith(['lines_veils', 'warning text-white'])]
    #[TestWith(['open_door', 'success'])]
    #[TestWith(['other', 'secondary'])]
    public function testCssClass(string $value, string $expected): void
    {
        self::assertSame($expected, SafetyToolType::from($value)->getCssClass());
    }
}
