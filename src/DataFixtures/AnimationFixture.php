<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Animation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

class AnimationFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '7645788c-edde-4b51-9cb8-1c6f641ceffe' => [
                'name' => 'Animation de jeu',
                'description' => 'Lorem ipsum',
                'maxNumberOfParticipants' => 5,
                'creators' => [new Ref(User::class, 'user-admin')],
            ],
            '173be12f-228a-4da2-8d4c-29d096ef7c0a' => [
                'name' => 'Visitor animation',
                'description' => 'Lorem ipsum',
                'maxNumberOfParticipants' => 5,
                'creators' => [new Ref(User::class, 'user-visitor')],
            ],
            '75df22c8-567b-4e12-b1cf-ed77b7ac00f4' => [
                'name' => 'Concert',
                'description' => 'Lorem ipsum',
                'maxNumberOfParticipants' => 180,
                'creators' => [new Ref(User::class, 'user-admin')],
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return Animation::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'animation-';
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
}
