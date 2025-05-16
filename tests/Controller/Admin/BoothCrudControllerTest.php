<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\BoothCrudController;
use App\Controller\Admin\DashboardController;
use App\DataFixtures\BoothFixture;
use App\DataFixtures\RoomFixture;
use App\DataFixtures\Tools\Ref;
use App\Entity\Room;
use App\Tests\TestUtils\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

final class BoothCrudControllerTest extends AbstractCrudTestCase
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
        return BoothCrudController::class;
    }

    public function testIndexAdmin(): void
    {
        $this->runIndexPage(BoothFixture::getStaticData());
    }

    public function testIndexVisitor(): void
    {
        $this->runIndexPage(BoothFixture::filterByKeyAndValue('room', new Ref(Room::class, 'room-Custom Entry hall')), 'visitor');
        $this->runIndexPage(BoothFixture::filterData(static fn (array $data) => \str_starts_with($data['name'], 'Custom ')), 'visitor');
    }

    public function testNew(): void
    {
        $newData = [
            'name' => 'Test booth name',
            'room' => RoomFixture::getIdFromName('Hall jeux'),
            'maxNumberOfParticipants' => 10,
        ];

        $this->runNewFormSubmit($newData);
    }

    public function testEdit(): void
    {
        $newData = [
            'name' => 'Test new booth name',
            'room' => RoomFixture::getIdFromName('Hall jeux'),
            'maxNumberOfParticipants' => 10,
        ];

        $this->runEditFormSubmit(BoothFixture::getIdFromName('Table face pôle JdR 1'), $newData);
    }
}
