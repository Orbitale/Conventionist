<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\FloorCrudController;
use App\DataFixtures\FloorFixture;
use App\DataFixtures\Tools\Ref;
use App\DataFixtures\VenueFixture;
use App\Entity\Venue;
use App\Tests\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;
use PHPUnit\Framework\Attributes\DataProvider;

class FloorCrudControllerTest extends AbstractCrudTestCase
{
    use CrudTestFormAsserts;
    use GetUser;
    use Utils\TestAdminIndex;
    use Utils\TestAdminNew;
    use Utils\TestAdminEdit;

    protected static function getIndexColumnNames(): array
    {
        return ['name', 'venue'];
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function getControllerFqcn(): string
    {
        return FloorCrudController::class;
    }

    public function testIndexAdmin(): void
    {
        $this->runIndexPage(FloorFixture::getStaticData());
    }

    public function testIndexVisitor(): void
    {
        $this->runIndexPage(FloorFixture::filterByKeyAndValue('venue', new Ref(Venue::class, 'venue-Custom')), 'visitor');
    }

    public function testNew(): void
    {
        $newData = [
            'name' => 'Test floor name',
            'venue' => VenueFixture::getIdFromName('CPC'),
        ];

        $this->runNewFormSubmit($newData);
    }

    public function testEdit(): void
    {
        $newData = [
            'name' => 'Test new floor name',
            'venue' => VenueFixture::getIdFromName('CPC'),
        ];

        $this->runEditFormSubmit(FloorFixture::getIdFromName('RDC'), $newData);
    }
}
