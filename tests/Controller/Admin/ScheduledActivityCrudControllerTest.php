<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\ScheduledActivityCrudController;
use App\DataFixtures\ActivityFixture;
use App\DataFixtures\ScheduledActivityFixture;
use App\Tests\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class ScheduledActivityCrudControllerTest extends AbstractCrudTestCase
{
    use CrudTestFormAsserts;
    use GetUser;
    use Utils\TestAdminIndex;
    use Utils\TestAdminNew;
    use Utils\TestAdminEdit;

    protected static function getIndexColumnNames(): array
    {
        return ['activity', 'timeSlot'];
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function getControllerFqcn(): string
    {
        return ScheduledActivityCrudController::class;
    }

    public function testIndexAdmin(): void
    {
        $this->runIndexPage(ScheduledActivityFixture::getStaticData());
    }

    public function testIndexVisitor(): void
    {
        $data = \array_filter(
            ScheduledActivityFixture::getStaticData(),
            static fn(array $data) => $data['activity']->name === 'activity-Visitor activity'
        );

        $this->runIndexPage($data, 'visitor');
    }

    public function testNewAsAdmin(): void
    {
        $this->runNewFormSubmit([
            'activity' => ActivityFixture::getIdFromName('Activity de jeu'),
            'timeSlot' => 'ed52861f-3cfd-47df-ac1d-ffaedf6910e8',
        ], 'admin', ['activity', 'timeSlot']);
    }

    public function testNewAsNonAdmin(): void
    {
        $this->runNewFormSubmit([
            'activity' => ActivityFixture::getIdFromName('Visitor activity'),
            'timeSlot' => 'ed52861f-3cfd-47df-ac1d-ffaedf6910e8',
        ], 'visitor', ['activity', 'timeSlot']);
    }
}
