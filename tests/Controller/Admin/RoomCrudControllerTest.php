<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\RoomCrudController;
use App\DataFixtures\FloorFixture;
use App\DataFixtures\RoomFixture;
use App\Tests\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class RoomCrudControllerTest extends AbstractCrudTestCase
{
    use CrudTestFormAsserts;
    use GetUser;
    use TestAdminIndex;
    use TestAdminNew;
    use TestAdminEdit;

    protected static function getIndexColumnNames(): array
    {
        return ['name', 'floor'];
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function getControllerFqcn(): string
    {
        return RoomCrudController::class;
    }

    public function testIndex(): void
    {
        $this->runIndexPage(RoomFixture::getStaticData());
    }

    public function testNew(): void
    {
        $newData = [
            'name' => 'Test room name',
            'floor' => FloorFixture::getIdFromName('RDC'),
        ];

        $this->runNewFormSubmit($newData);
    }

    public function testEdit(): void
    {
        $newData = [
            'name' => 'Test new room name',
            'floor' => FloorFixture::getIdFromName('Entresol'),
        ];

        $this->runEditFormSubmit(RoomFixture::getIdFromName('Hall principal'), $newData);
    }
}
