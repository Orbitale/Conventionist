<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\EventCrudController;
use App\DataFixtures\EventFixture;
use App\DataFixtures\VenueFixture;
use App\Tests\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;
use PHPUnit\Framework\Attributes\DataProvider;

class EventCrudControllerTest extends AbstractCrudTestCase
{
    use CrudTestFormAsserts;
    use GetUser;
    use Utils\TestAdminIndex;
    use Utils\TestAdminNew;
    use Utils\TestAdminEdit;

    protected static function getIndexColumnNames(): array
    {
        return ['name'];
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function getControllerFqcn(): string
    {
        return EventCrudController::class;
    }

    #[DataProvider('provideUsernames')]
    public function testIndex(string $username): void
    {
        $this->runIndexPage(EventFixture::getStaticData(), $username);
    }

    public static function provideUsernames(): iterable
    {
        yield 'admin' => ['admin'];
        yield 'conference_organizer' => ['conference_organizer'];
    }

    public function testNewAsAdmin(): void
    {
        $this->runNewFormSubmit([
            'name' => 'Test event name',
            'address' => 'CPC',
            'description' => 'Hello world',
            'startsAt' => '2055-01-01T00:00',
            'endsAt' => '2055-01-05T00:00',
            'venue' => VenueFixture::getIdFromName('CPC'),
        ]);
    }

    public function testNewAsNonAdmin(): void
    {
        $this->runNewFormSubmit([
            'name' => 'Test event name',
            'address' => 'CPC',
            'description' => 'Hello world',
            'startsAt' => '2055-01-01T00:00',
            'endsAt' => '2055-01-05T00:00',
            'venue' => VenueFixture::getIdFromName('CPC'),
        ], 'conference_organizer');
    }

    public function testEdit(): void
    {
        $this->runEditFormSubmit(EventFixture::getIdFromName('TDC 2025'), [
            'name' => 'TDC 2055',
            'address' => 'CPC',
            'description' => 'Hello world',
            'startsAt' => '2055-01-01T00:00',
            'endsAt' => '2055-01-05T00:00',
            'venue' => VenueFixture::getIdFromName('CPC'),
        ]);
    }
}
