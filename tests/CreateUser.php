<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

trait CreateUser
{
    public static function createUser(ContainerInterface $container): User
    {
        $user = new User();
        $user->setUsername('foo');
        $user->setEmail('foo@test.localhost');

        $user->setPassword($container->get(UserPasswordHasherInterface::class)->hashPassword($user, 'foo'));
        $em = $container->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        return $user;
    }
}
