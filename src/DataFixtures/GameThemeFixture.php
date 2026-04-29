<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\Entity\GameTheme;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class GameThemeFixture extends ArrayFixture implements ORMFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '0194a8b0-0002-7000-8000-000000000001' => [
                'name' => 'Fantasy',
                'description' => 'Swords and sorcery',
            ],
            '0194a8b0-0002-7000-8000-000000000002' => [
                'name' => 'Sci-Fi',
                'description' => 'Space and future tech',
            ],
            '0194a8b0-0002-7000-8000-000000000003' => [
                'name' => 'Horror',
                'description' => 'Scary scenarios',
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return GameTheme::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'game-theme-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }
}
