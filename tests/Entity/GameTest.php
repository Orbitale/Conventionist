<?php

namespace App\Tests\Entity;

use App\Entity\Game;
use App\Entity\GameCategory;
use App\Entity\GameTheme;
use App\Enum\GameComplexity;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    public function testConstructInitialisesCollectionsAndId(): void
    {
        $game = new Game();

        self::assertNotEmpty($game->getId());
        self::assertCount(0, $game->getCategories());
        self::assertCount(0, $game->getThemes());
        self::assertCount(0, $game->getCreators());
    }

    public function testTitleAndComplexity(): void
    {
        $game = new Game();
        $game->setTitle('My Game');
        $game->setComplexity(GameComplexity::MEDIUM);

        self::assertSame('My Game', $game->getTitle());
        self::assertSame('My Game', (string) $game);
        self::assertSame(GameComplexity::MEDIUM, $game->getComplexity());
    }

    public function testAddAndRemoveCategoriesAndThemes(): void
    {
        $game = new Game();
        $category = new GameCategory();
        $theme = new GameTheme();

        $game->addCategory($category);
        $game->addCategory($category);
        $game->addTheme($theme);

        self::assertCount(1, $game->getCategories());
        self::assertCount(1, $game->getThemes());

        $game->removeCategory($category);
        $game->removeTheme($theme);

        self::assertCount(0, $game->getCategories());
        self::assertCount(0, $game->getThemes());
    }
}
