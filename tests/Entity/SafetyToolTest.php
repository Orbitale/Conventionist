<?php

namespace App\Tests\Entity;

use App\Entity\SafetyTool;
use App\Enum\SafetyToolType;
use PHPUnit\Framework\TestCase;

final class SafetyToolTest extends TestCase
{
    public function testDefaults(): void
    {
        $tool = new SafetyTool();

        self::assertNotEmpty($tool->getId());
        self::assertSame(SafetyToolType::OTHER, $tool->getType());
        self::assertSame('', $tool->getContentWarnings());
    }

    public function testSetters(): void
    {
        $tool = new SafetyTool();
        $tool->setName('X-Card');
        $tool->setType(SafetyToolType::X_CARD);
        $tool->setContentWarnings('violence, gore');

        self::assertSame('X-Card', $tool->getName());
        self::assertSame(SafetyToolType::X_CARD, $tool->getType());
        self::assertSame('violence, gore', $tool->getContentWarnings());
    }
}
