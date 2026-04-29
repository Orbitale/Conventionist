<?php

namespace App\Tests\Enum;

use App\Enum\EventRequestStatus;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class EventRequestStatusTest extends TestCase
{
    #[TestWith(['submitted', false])]
    #[TestWith(['approved', true])]
    #[TestWith(['rejected', true])]
    #[TestWith(['withdrawn', true])]
    public function testIsFinal(string $type, bool $expected): void
    {
        self::assertSame($expected, EventRequestStatus::from($type)->isFinal());
    }

    #[TestWith(['submitted', 'warning text-white'])]
    #[TestWith(['approved', 'success'])]
    #[TestWith(['rejected', 'danger'])]
    #[TestWith(['withdrawn', 'secondary'])]
    public function testCssClass(string $type, string $expected): void
    {
        self::assertSame($expected, EventRequestStatus::from($type)->getCssClass());
    }
}
