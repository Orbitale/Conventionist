<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\ActivityCrudController;
use App\Controller\Admin\DashboardController;
use App\DataFixtures\ActivityFixture;
use App\DataFixtures\Tools\Ref;
use App\Tests\TestUtils\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;
use PHPUnit\Framework\Attributes\TestWith;

final class ActivityCrudControllerTest extends AbstractCrudTestCase
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
        return ActivityCrudController::class;
    }

    public function testIndexAdmin(): void
    {
        $this->runIndexPage(ActivityFixture::getStaticData());
    }

    #[TestWith(['visitor'])]
    public function testIndexNonAdmin(string $username): void
    {
        $data = \array_filter(
            ActivityFixture::getStaticData(),
            static fn ($data) => \array_any(
                \iterator_to_array($data['creators']),
                static fn (Ref $creator) => $creator->name === 'user-'.$username
            )
        );
        $this->runIndexPage($data, $username);
    }

    public function testNewAsAdmin(): void
    {
        $this->runNewFormSubmit([
            'name' => 'New Activity',
            'maxNumberOfParticipants' => 5,
        ]);
    }

    public function testNewAsNonAdmin(): void
    {
        $this->runNewFormSubmit([
            'name' => 'New Activity',
            'maxNumberOfParticipants' => 5,
        ], 'visitor');
    }

    public function testEdit(): void
    {
        $this->runEditFormSubmit(ActivityFixture::getIdFromName('Activité de jeu'), [
            'name' => 'New Activity name',
            'maxNumberOfParticipants' => 20,
        ]);
    }
}
