<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

trait GetUser
{
    protected function getUser(string $username = 'admin'): User
    {
        if (!$this instanceof KernelTestCase) {
            throw new \RuntimeException(\sprintf('Trait "%s" used by class "%s" can only be used in an instance of "%s".', self::class, static::class, KernelTestCase::class));
        }

        return self::getContainer()->get(UserRepository::class)->loadUserByIdentifier($username);
    }
}
