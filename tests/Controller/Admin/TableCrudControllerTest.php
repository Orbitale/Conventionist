<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\TableCrudController;
use App\DataFixtures\RoomFixture;
use App\DataFixtures\TableFixture;
use App\Tests\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class TableCrudControllerTest extends AbstractCrudTestCase
{
    use CrudTestFormAsserts;
    use GetUser;
    use TestAdminIndex;
    use TestAdminNew;
    use TestAdminEdit;

    protected static function getIndexColumnNames(): array
    {
        return ['name', 'maxNumberOfParticipants'];
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function getControllerFqcn(): string
    {
        return TableCrudController::class;
    }

    public function testIndex(): void
    {
        $this->runIndexPage(TableFixture::getStaticData());
    }

    public function testNew(): void
    {
        $newData = [
            'name' => 'Test table name',
            'room' => RoomFixture::getIdFromName('Hall jeux'),
            'maxNumberOfParticipants' => 10,
        ];

        $this->runNewFormSubmit($newData);
    }

    public function testEdit(): void
    {
        $newData = [
            'name' => 'Test new table name',
            'room' => RoomFixture::getIdFromName('Hall jeux'),
            'maxNumberOfParticipants' => 10,
        ];

        $this->runEditFormSubmit(TableFixture::getIdFromName('Table face pôle JdR 1'), $newData);
    }
}
