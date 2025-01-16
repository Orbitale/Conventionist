<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\EventCrudController;
use App\DataFixtures\VenueFixture;
use App\Tests\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class EventCrudControllerTest extends AbstractCrudTestCase
{
    use CrudTestFormAsserts;
    use GetUser;
    use TestAdminIndex;
    use TestAdminNew;
    use TestAdminEdit;

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

    public function testIndex(): void
    {
        $this->runIndexPage([
            'b715276f-f7df-42ee-82f8-c21b05d2da2d' => [
                'name' => 'TDC 2025',
            ],
        ]);
    }

    public function testNew(): void
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

    public function testEdit(): void
    {
        $this->runEditFormSubmit('b715276f-f7df-42ee-82f8-c21b05d2da2d', [
            'name' => 'TDC 2055',
            'address' => 'CPC',
            'description' => 'Hello world',
            'startsAt' => '2055-01-01T00:00',
            'endsAt' => '2055-01-05T00:00',
            'venue' => VenueFixture::getIdFromName('CPC'),
        ]);
    }
}
