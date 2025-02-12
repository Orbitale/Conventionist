<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\AnimationCrudController;
use App\DataFixtures\AnimationFixture;
use App\DataFixtures\Tools\Ref;
use App\Tests\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

class AnimationCrudControllerTest extends AbstractCrudTestCase
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
        return AnimationCrudController::class;
    }

    public function testIndexAdmin(): void
    {
        $this->runIndexPage(AnimationFixture::getStaticData());
    }

    #[TestWith(['visitor'])]
    public function testIndexNonAdmin(string $username): void
    {
        $data = \array_filter(
            AnimationFixture::getStaticData(),
            static fn($data) => \array_any(
                \iterator_to_array($data['creators']),
                static fn(Ref $creator) => $creator->name === 'user-' . $username
            )
        );
        $this->runIndexPage($data, $username);
    }

    public function testNewAsAdmin(): void
    {
        $this->runNewFormSubmit([
            'name' => 'New Animation',
            'maxNumberOfParticipants' => 5,
        ]);
    }

    public function testNewAsNonAdmin(): void
    {
        $this->runNewFormSubmit([
            'name' => 'New Animation',
            'maxNumberOfParticipants' => 5,
        ], 'visitor');
    }

    public function testEdit(): void
    {
        $this->runEditFormSubmit(AnimationFixture::getIdFromName('Animation de jeu'), [
            'name' => 'New Animation name',
            'maxNumberOfParticipants' => 20,
        ]);
    }
}
