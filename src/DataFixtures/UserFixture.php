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
                'locale' => 'fr_FR',
            ],
            [
                'id' => 'a50196ec-0571-4cef-9f12-3bfaef3d094e',
                'username' => 'other_admin',
                'email' => 'other_admin@test.localhost',
                'password' => $this->hasher->getPasswordHasher(User::class)->hash('other_admin'),
                'roles' => ['ROLE_ADMIN'],
                'timezone' => 'Europe/Paris',
                'isVerified' => true,
                'isEmailConfirmed' => true,
                'locale' => 'en_GB',
            ],
            [
                'id' => '9a857dfd-814a-4c8e-b767-91897c69e51e',
                'username' => 'visitor',
                'email' => 'visitor@test.localhost',
                'password' => $this->hasher->getPasswordHasher(User::class)->hash('visitor'),
                'roles' => [],
                'timezone' => 'Europe/Paris',
                'isVerified' => true,
                'isEmailConfirmed' => true,
                'locale' => 'fr-FR',
            ],
        ];
    }
}
