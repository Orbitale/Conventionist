<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Organization;
use App\Entity\User;
use App\Entity\UserGroup;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class UserGroupFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    protected function getEntityClass(): string
    {
        return UserGroup::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'user-group-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            OrganizationFixture::class,
        ];
    }

    public static function getStaticData(): array
    {
        return [
            '0192a001-0000-7000-8000-000000000001' => [
                'name' => 'Acme Staff',
                'slug' => 'acme-staff',
                'description' => 'Staff members of Acme Org.',
                'organization' => new Ref(Organization::class, 'organization-Acme Org'),
                'creators' => [new Ref(User::class, 'user-ash')],
            ],
            '0192a001-0000-7000-8000-000000000002' => [
                'name' => 'Indie Volunteers',
                'slug' => 'indie-volunteers',
                'description' => 'Volunteers helping Indie Collective.',
                'organization' => new Ref(Organization::class, 'organization-Indie Collective'),
                'creators' => [new Ref(User::class, 'user-visitor')],
            ],
        ];
    }
}
