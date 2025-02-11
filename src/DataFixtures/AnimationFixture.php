<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Animation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;
use Symfony\Component\Uid\Uuid;

class AnimationFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            Uuid::v7()->toString() => [
                'name' => 'Animation de jeu',
                'description' => 'Lorem ipsum',
                'maxNumberOfParticipants' => 5,
                'creators' => [new Ref(User::class, 'user-admin')],
            ],
            Uuid::v7()->toString() => [
                'name' => 'Visitor animation',
                'description' => 'Lorem ipsum',
                'maxNumberOfParticipants' => 5,
                'creators' => [new Ref(User::class, 'user-visitor')],
            ],
            Uuid::v7()->toString() => [
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
