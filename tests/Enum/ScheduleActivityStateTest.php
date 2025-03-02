<?php

namespace App\Tests\Enum;

use App\Enum\ScheduleActivityState;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class ScheduleActivityStateTest extends TestCase
{
    #[TestWith(['created', '#343a40'])]
    #[TestWith(['pending_review', '#ffc107'])]
    #[TestWith(['rejected', '#dc3545'])]
    #[TestWith(['accepted', '#198754'])]
    public function testColor(string $type, string $expectedColor): void
    {
        self::assertSame($expectedColor, ScheduleActivityState::from($type)->getColor());
    }

    #[TestWith(['created', 'secondary'])]
    #[TestWith(['pending_review', 'warning text-white'])]
    #[TestWith(['rejected', 'danger'])]
    #[TestWith(['accepted', 'success'])]
    public function testCssClass(string $type, string $expectedColor): void
    {
        self::assertSame($expectedColor, ScheduleActivityState::from($type)->getCssClass());
    }
}
