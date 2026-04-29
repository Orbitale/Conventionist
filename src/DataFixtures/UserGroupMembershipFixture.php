<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Entity\UserGroupMembership;
use App\Enum\UserGroupRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class UserGroupMembershipFixture extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            UserGroupFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $rows = [
            ['user-ash', 'user-group-Acme Staff', UserGroupRole::OWNER],
            ['user-visitor', 'user-group-Acme Staff', UserGroupRole::MEMBER],
            ['user-visitor', 'user-group-Indie Volunteers', UserGroupRole::OWNER],
            ['user-ash', 'user-group-Indie Volunteers', UserGroupRole::LEADER],
        ];

        foreach ($rows as [$userRef, $groupRef, $role]) {
            /** @var User $user */
            $user = $this->getReference($userRef, User::class);
            /** @var UserGroup $group */
            $group = $this->getReference($groupRef, UserGroup::class);

            $membership = new UserGroupMembership();
            $membership->setUser($user);
            $membership->setUserGroup($group);
            $membership->setRole($role);

            $manager->persist($membership);
        }

        $manager->flush();
    }
}
