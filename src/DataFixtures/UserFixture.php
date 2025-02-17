<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

final class UserFixture extends ArrayFixture implements ORMFixtureInterface
{
    use GetObjectsFromData;

    private static PasswordHasherFactoryInterface $hasher;

    public static function getStaticData(): array
    {
        return [
            '41f63479-09b0-41a6-b411-2ec5a5f98895' => [
                'username' => 'admin',
                'email' => 'admin@test.localhost',
                'password' => fn () => self::$hasher->getPasswordHasher(User::class)->hash('admin'),
                'roles' => ['ROLE_ADMIN'],
                'timezone' => 'Europe/Paris',
                'emailConfirmed' => new \DateTimeImmutable(),
                'locale' => 'fr',
            ],
            '071cca41-faf6-4e7a-bc0f-a9d07d9bdb0e' => [
                'username' => 'ash',
                'email' => 'ash@test.localhost',
                'password' => fn () => self::$hasher->getPasswordHasher(User::class)->hash('ash'),
                'roles' => [],
                'timezone' => 'Europe/Paris',
                'emailConfirmed' => new \DateTimeImmutable(),
                'locale' => 'fr',
            ],
            '1042e551-2d75-4ff4-bdfa-4f3046041d36' => [
                'username' => 'visitor',
                'email' => 'visitor@test.localhost',
                'password' => fn () => self::$hasher->getPasswordHasher(User::class)->hash('visitor'),
                'roles' => [],
                'timezone' => 'Europe/Paris',
                'emailConfirmed' => new \DateTimeImmutable(),
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
