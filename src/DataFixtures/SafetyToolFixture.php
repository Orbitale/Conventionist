<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\Entity\SafetyTool;
use App\Enum\SafetyToolType;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class SafetyToolFixture extends ArrayFixture implements ORMFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '0194a8b0-0004-7000-8000-000000000001' => [
                'name' => 'X-Card',
                'description' => 'Tap the card to remove content from the game, no questions asked.',
                'type' => SafetyToolType::X_CARD,
                'contentWarnings' => '',
            ],
            '0194a8b0-0004-7000-8000-000000000002' => [
                'name' => 'Lines & Veils',
                'description' => 'Lines are hard limits; veils fade to black.',
                'type' => SafetyToolType::LINES_VEILS,
                'contentWarnings' => '',
            ],
            '0194a8b0-0004-7000-8000-000000000003' => [
                'name' => 'Open Door',
                'description' => 'Any player may leave the session at any time without explanation.',
                'type' => SafetyToolType::OPEN_DOOR,
                'contentWarnings' => '',
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return SafetyTool::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'safety-tool-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }
}
