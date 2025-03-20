<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\VenueCrudController;
use App\DataFixtures\VenueFixture;
use App\Tests\TestUtils\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

final class VenueCrudControllerTest extends AbstractCrudTestCase
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
        return VenueCrudController::class;
    }

    public function testIndexAdmin(): void
    {
        $this->runIndexPage(VenueFixture::getStaticData());
    }

    public function testIndexVisitor(): void
    {
        $this->runIndexPage(VenueFixture::filterByKeyAndValue('name', 'Custom'), 'visitor');
    }

    public function testNew(): void
    {
        $this->runNewFormSubmit([
            'name' => 'Test venue name',
            'address1' => 'Somewhere',
        ]);
    }

    public function testEdit(): void
    {
        $this->runEditFormSubmit(VenueFixture::getIdFromName('CPC'), [
            'name' => 'Test new floor name',
            'city' => 'Le Puy',
            'address1' => 'Quelque part',
        ]);
    }
}
