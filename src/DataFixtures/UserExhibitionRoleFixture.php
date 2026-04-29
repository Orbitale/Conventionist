<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\UserExhibitionRole;
use App\Enum\ExhibitionRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class UserExhibitionRoleFixture extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            EventFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $rows = [
            ['user-ash', 'event-TDC 2025', ExhibitionRole::ORGANIZER],
            ['user-visitor', 'event-TDC 2025', ExhibitionRole::STAFF],
            ['user-visitor', 'event-Custom event', ExhibitionRole::ORGANIZER],
        ];

        foreach ($rows as [$userRef, $eventRef, $role]) {
            /** @var User $user */
            $user = $this->getReference($userRef, User::class);
            /** @var Event $event */
            $event = $this->getReference($eventRef, Event::class);

            $r = new UserExhibitionRole();
            $r->setUser($user);
            $r->setEvent($event);
            $r->setRole($role);

            $manager->persist($r);
        }

        $manager->flush();
    }
}
