<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\UserChecker;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserCheckerTest extends WebTestCase
{
    public function testPostAuthVerified(): void
    {
        $this->expectException(CustomUserMessageAccountStatusException::class);
        $this->expectExceptionMessage('authentication.error.user_not_verified');

        $class = new UserChecker();
        $user = new User();
        $class->checkPostAuth($user);
    }

    public function testPostAuthEmailConfirmed(): void
    {
        $this->expectException(CustomUserMessageAccountStatusException::class);
        $this->expectExceptionMessage('authentication.error.user_email_not_confirmed');

        $class = new UserChecker();
        $user = new User();
        $user->setIsVerified(true);
        $class->checkPostAuth($user);
    }

    public function testValidCase(): void
    {
        try {
            $class = new UserChecker();
            $user = new User();
            $user->setIsVerified(true);
            $user->setEmailConfirmed(true);
            $class->checkPostAuth($user);
            self::assertTrue(true);
        } catch (CustomUserMessageAccountStatusException) {
            self::fail('User check thrown exception while expected none.');
        }
    }

    public function testFunctionalVerified(): void
    {
        $client = self::createClient();
        $user = $this->createUser($client->getContainer());

        $client->loginUser($user);
        $client->request('GET', '/admin');

        self::assertResponseRedirects('/login');
    }

    public function testFunctionalEmailConfirmed(): void
    {
        $client = self::createClient();
        $user = $this->createUser($client->getContainer());

        $client->loginUser($user);
        $client->request('GET', '/admin');

        self::assertResponseRedirects('/login');
    }

    public function createUser(ContainerInterface $container): User
    {
        $user = new User();
        $user->setUsername('foo');
        $user->setEmail('foo@test.localhost');

        $user->setPassword('');
        $em = $container->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        return $user;
    }
}
