<?php

namespace App\Tests\Enum;

use App\Enum\EventRegistrationStatus;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class EventRegistrationStatusTest extends TestCase
{
    #[TestWith(['pending', '#ffc107'])]
    #[TestWith(['confirmed', '#198754'])]
    #[TestWith(['cancelled', '#dc3545'])]
    #[TestWith(['waitlist', '#6c757d'])]
    #[TestWith(['checked_in', '#0d6efd'])]
    public function testColor(string $type, string $expected): void
    {
        self::assertSame($expected, EventRegistrationStatus::from($type)->getColor());
    }

    #[TestWith(['pending', true])]
    #[TestWith(['confirmed', true])]
    #[TestWith(['checked_in', true])]
    #[TestWith(['waitlist', true])]
    #[TestWith(['cancelled', false])]
    public function testIsActive(string $type, bool $expected): void
    {
        self::assertSame($expected, EventRegistrationStatus::from($type)->isActive());
    }
}
