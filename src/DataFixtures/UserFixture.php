<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Uid\Uuid;

class UserFixture extends ArrayFixture implements ORMFixtureInterface
{
    use GetObjectsFromData;

    private static PasswordHasherFactoryInterface $hasher;

    public static function getStaticData(): array
    {
        return [
            Uuid::v7()->toString() => [
                'username' => 'admin',
                'email' => 'admin@test.localhost',
                'password' => fn () => self::$hasher->getPasswordHasher(User::class)->hash('admin'),
                'roles' => ['ROLE_ADMIN'],
                'timezone' => 'Europe/Paris',
                'isVerified' => true,
                'isEmailConfirmed' => true,
                'locale' => 'fr',
            ],
            Uuid::v7()->toString() => [
                'username' => 'conference_organizer',
                'email' => 'conference_organizer@test.localhost',
                'password' => fn () => self::$hasher->getPasswordHasher(User::class)->hash('conference_organizer'),
                'roles' => ['ROLE_CONFERENCE_ORGANIZER'],
                'timezone' => 'Europe/Paris',
                'isVerified' => true,
                'isEmailConfirmed' => true,
                'locale' => 'fr',
            ],
            Uuid::v7()->toString() => [
                'username' => 'visitor',
                'email' => 'visitor@test.localhost',
                'password' => fn () => self::$hasher->getPasswordHasher(User::class)->hash('visitor'),
                'roles' => ['ROLE_VISITOR'],
                'timezone' => 'Europe/Paris',
                'isVerified' => true,
                'isEmailConfirmed' => true,
                'locale' => 'fr',
            ],
            Uuid::v7()->toString() => [
                'username' => 'venue_manager',
                'email' => 'venue_manager@test.localhost',
                'password' => fn () => self::$hasher->getPasswordHasher(User::class)->hash('venue_manager'),
                'roles' => ['ROLE_VENUE_MANAGER'],
                'timezone' => 'Europe/Paris',
                'isVerified' => true,
                'isEmailConfirmed' => true,
                'locale' => 'fr',
            ],
        ];
    }

    public function __construct(PasswordHasherFactoryInterface $hasher)
    {
        self::$hasher = $hasher;
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
}
