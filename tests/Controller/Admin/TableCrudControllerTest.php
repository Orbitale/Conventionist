<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\TableCrudController;
use App\DataFixtures\RoomFixture;
use App\DataFixtures\TableFixture;
use App\DataFixtures\Tools\Ref;
use App\Entity\Room;
use App\Tests\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class TableCrudControllerTest extends AbstractCrudTestCase
{
    use CrudTestFormAsserts;
    use GetUser;
    use Utils\TestAdminIndex;
    use Utils\TestAdminNew;
    use Utils\TestAdminEdit;

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

    public function testIndexAdmin(): void
    {
        $this->runIndexPage(TableFixture::getStaticData());
    }

    public function testIndexVisitor(): void
    {
        $this->runIndexPage(TableFixture::filterByKeyAndValue('room', new Ref(Room::class, 'room-Custom Entry hall')), 'visitor');
        $this->runIndexPage(TableFixture::filterData(static fn (array $data) => \str_starts_with($data['name'], 'Custom ')), 'visitor');
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
