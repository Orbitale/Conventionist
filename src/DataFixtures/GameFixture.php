<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Game;
use App\Entity\GameCategory;
use App\Entity\GameTheme;
use App\Entity\User;
use App\Enum\GameComplexity;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class GameFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '0194a8b0-0003-7000-8000-000000000001' => [
                'title' => 'Dungeons & Dragons 5e',
                'description' => 'Classic fantasy RPG',
                'publisher' => 'Wizards of the Coast',
                'minPlayers' => 3,
                'maxPlayers' => 6,
                'durationMinutes' => 240,
                'complexity' => GameComplexity::MEDIUM,
                'categories' => [new Ref(GameCategory::class, 'game-category-Role-Playing Game')],
                'themes' => [new Ref(GameTheme::class, 'game-theme-Fantasy')],
                'creators' => [new Ref(User::class, 'user-visitor')],
            ],
            '0194a8b0-0003-7000-8000-000000000002' => [
                'title' => 'Call of Cthulhu',
                'description' => 'Lovecraftian horror RPG',
                'publisher' => 'Chaosium',
                'minPlayers' => 2,
                'maxPlayers' => 6,
                'durationMinutes' => 180,
                'complexity' => GameComplexity::MEDIUM_HEAVY,
                'categories' => [new Ref(GameCategory::class, 'game-category-Role-Playing Game')],
                'themes' => [new Ref(GameTheme::class, 'game-theme-Horror')],
                'creators' => new ArrayCollection(),
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return Game::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'game-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getTitle';
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            GameCategoryFixture::class,
            GameThemeFixture::class,
        ];
    }
}
