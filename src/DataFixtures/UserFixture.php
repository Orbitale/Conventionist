<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class UserFixture extends ArrayFixture implements ORMFixtureInterface
{
    public function __construct(private readonly PasswordHasherFactoryInterface $hasher)
    {
        parent::__construct();
    }

    protected function getEntityClass(): string
    {
        return User::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'user-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getUsername';
    }

    protected function getObjects(): iterable
    {
        return [
            [
                'id' => 'dd45dfe9-1526-4c60-b9e7-d4d306627acb',
                'username' => 'admin',
                'email' => 'admin@test.localhost',
                'password' => $this->hasher->getPasswordHasher(User::class)->hash('admin'),
                'roles' => ['ROLE_ADMIN'],
                'timezone' => 'Europe/Paris',
                'isVerified' => true,
                'isEmailConfirmed' => true,
                'locale' => 'fr',
            ],
            [
                'id' => 'a50196ec-0571-4cef-9f12-3bfaef3d094e',
                'username' => 'conference_organizer',
                'email' => 'conference_organizer@test.localhost',
                'password' => $this->hasher->getPasswordHasher(User::class)->hash('conference_organizer'),
                'roles' => ['ROLE_CONFERENCE_ORGANIZER'],
                'timezone' => 'Europe/Paris',
                'isVerified' => true,
                'isEmailConfirmed' => true,
                'locale' => 'fr',
            ],
            [
                'id' => '9a857dfd-814a-4c8e-b767-91897c69e51e',
                'username' => 'visitor',
                'email' => 'visitor@test.localhost',
                'password' => $this->hasher->getPasswordHasher(User::class)->hash('visitor'),
                'roles' => ['ROLE_VISITOR'],
                'timezone' => 'Europe/Paris',
                'isVerified' => true,
                'isEmailConfirmed' => true,
                'locale' => 'fr',
            ],
            [
                'id' => 'b4500766-0d41-4004-9922-c78f92e22c3a',
                'username' => 'venue_manager',
                'email' => 'venue_manager@test.localhost',
                'password' => $this->hasher->getPasswordHasher(User::class)->hash('venue_manager'),
                'roles' => ['ROLE_EVENT_VENUE_MANAGER'],
                'timezone' => 'Europe/Paris',
                'isVerified' => true,
                'isEmailConfirmed' => true,
                'locale' => 'fr',
            ],
        ];
    }
}
