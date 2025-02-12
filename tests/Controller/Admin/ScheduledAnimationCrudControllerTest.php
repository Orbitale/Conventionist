<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\ScheduledAnimationCrudController;
use App\DataFixtures\AnimationFixture;
use App\DataFixtures\ScheduledAnimationFixture;
use App\Tests\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class ScheduledAnimationCrudControllerTest extends AbstractCrudTestCase
{
    use CrudTestFormAsserts;
    use GetUser;
    use Utils\TestAdminIndex;
    use Utils\TestAdminNew;
    use Utils\TestAdminEdit;

    protected static function getIndexColumnNames(): array
    {
        return ['animation', 'timeSlot'];
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function getControllerFqcn(): string
    {
        return ScheduledAnimationCrudController::class;
    }

    public function testIndexAdmin(): void
    {
        $this->runIndexPage(ScheduledAnimationFixture::getStaticData());
    }

    public function testIndexVisitor(): void
    {
        $data = \array_filter(
            ScheduledAnimationFixture::getStaticData(),
            static fn(array $data) => $data['animation']->name === 'animation-Visitor animation'
        );

        $this->runIndexPage($data, 'visitor');
    }

    public function testNewAsAdmin(): void
    {
        $this->runNewFormSubmit([
            'animation' => AnimationFixture::getIdFromName('Animation de jeu'),
            'timeSlot' => 'ed52861f-3cfd-47df-ac1d-ffaedf6910e8',
        ], 'admin', ['animation', 'timeSlot']);
    }

    public function testNewAsNonAdmin(): void
    {
        $this->runNewFormSubmit([
            'animation' => AnimationFixture::getIdFromName('Visitor animation'),
            'timeSlot' => 'ed52861f-3cfd-47df-ac1d-ffaedf6910e8',
        ], 'visitor', ['animation', 'timeSlot']);
    }
}
