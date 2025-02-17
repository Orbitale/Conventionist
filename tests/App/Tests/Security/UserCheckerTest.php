<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\UserChecker;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class UserCheckerTest extends WebTestCase
{
    public function testPostAuthEmailConfirmed(): void
    {
        $this->expectException(CustomUserMessageAccountStatusException::class);
        $this->expectExceptionMessage('authentication.error.user_email_not_confirmed');

        $class = new UserChecker();
        $user = new User();
        $class->checkPostAuth($user);
    }

    public function testValidCase(): void
    {
        try {
            $class = new UserChecker();
            $user = new User();
            $user->setEmailConfirmed(true);
            $class->checkPostAuth($user);
            self::assertTrue(true);
        } catch (CustomUserMessageAccountStatusException) {
            self::fail('User check thrown exception while expected none.');
        }
    }

    public function testFunctionalEmailConfirmed(): void
    {
        $client = self::createClient();
        $user = $this->createUser($client->getContainer());

        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('.login-wrapper form')->form();
        $client->submit($form, [
            'username' => $user->getUsername(),
            'password' => 'foo',
        ]);

        self::assertResponseRedirects('/login');
        $client->followRedirect();
        $alert = $client->getCrawler()->filter('.alert.alert-danger');
        self::assertSame(1, $alert->count());
        self::assertSame('Your e-mail address is not validated', \trim($alert->html()));
    }

    public function createUser(ContainerInterface $container): User
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
