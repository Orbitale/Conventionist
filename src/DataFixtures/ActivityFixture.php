<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Activity;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

class ActivityFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '5b555f8d-3fbd-4ba4-97c1-dd673b5109d6' => [
                'name' => 'Activity de jeu',
                'description' => 'Lorem ipsum',
                'maxNumberOfParticipants' => 5,
                'creators' => new ArrayCollection(),
            ],
            '6d443a40-6c42-4ced-bb1c-a285e415a768' => [
                'name' => 'Visitor activity',
                'description' => 'Lorem ipsum',
                'maxNumberOfParticipants' => 5,
                'creators' => [new Ref(User::class, 'user-visitor')],
            ],
            'd5ca41b2-72ac-4b48-b999-4689bee45d5f' => [
                'name' => 'Concert',
                'description' => 'Lorem ipsum',
                'maxNumberOfParticipants' => 180,
                'creators' => new ArrayCollection(),
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return Activity::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'activity-';
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
