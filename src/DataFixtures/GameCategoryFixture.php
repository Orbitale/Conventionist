<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\Entity\GameCategory;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class GameCategoryFixture extends ArrayFixture implements ORMFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '0194a8b0-0001-7000-8000-000000000001' => [
                'name' => 'Role-Playing Game',
                'description' => 'Tabletop RPGs',
            ],
            '0194a8b0-0001-7000-8000-000000000002' => [
                'name' => 'Board Game',
                'description' => 'Classic board games',
            ],
            '0194a8b0-0001-7000-8000-000000000003' => [
                'name' => 'Card Game',
                'description' => 'Card-based games',
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return GameCategory::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'game-category-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }
}
