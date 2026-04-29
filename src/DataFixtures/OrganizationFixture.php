<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Organization;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class OrganizationFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    protected function getEntityClass(): string
    {
        return Organization::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'organization-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }

    public static function getStaticData(): array
    {
        return [
            '0192a000-0000-7000-8000-000000000001' => [
                'name' => 'Acme Org',
                'slug' => 'acme-org',
                'description' => 'The Acme organization, running many conventions.',
                'website' => 'https://acme.test',
                'creators' => [new Ref(User::class, 'user-ash')],
            ],
            '0192a000-0000-7000-8000-000000000002' => [
                'name' => 'Indie Collective',
                'slug' => 'indie-collective',
                'description' => 'Small indie group organizing experimental events.',
                'website' => null,
                'creators' => [new Ref(User::class, 'user-visitor')],
            ],
        ];
    }
}
