<?php

namespace App\Tests\Controller;

use App\Controller\AuthController;
use App\Controller\RegistrationController;
use App\Repository\UserRepository;
use App\Tests\CreateUser;
use App\Tests\ProvidesLocales;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RegistrationControllerTest extends WebTestCase
{
    use ProvidesLocales;
    use CreateUser;

    #[DataProvider('provideLocales')]
    public function testRegister(string $locale): void
    {
        // Create user
        $client = self::createClient(server: ['HTTP_ACCEPT_LANGUAGE' => $locale]);
        $client->request('GET', RegistrationController::REGISTER_PATHS[$locale]);

        $repo = $client->getContainer()->get(UserRepository::class);

        $users = $repo->findAll();

        $translator = $client->getContainer()->get(TranslatorInterface::class);

        $registerText = $translator->trans('register', locale: $locale);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', $registerText);

        $username = 'new-user';
        $password = 'new-user-password';
        $email = 'new-user@test.localhost';

        $form = $client->getCrawler()->selectButton($registerText)->form([
            'registration_form[username]' => $username,
            'registration_form[email]' => $email,
            'registration_form[plainPassword]' => $password,
        ]);
        $client->submit($form);
        self::assertResponseStatusCodeSame(302);

        // Check user is in DB
        /** @var UserRepository $repo */
        $repo = $client->getContainer()->get(UserRepository::class);
        $newUsers = $repo->findAll();

        $created = \array_values(\array_diff($newUsers, $users));
        self::assertCount(1, $created);

        $created = $created[0];
        self::assertSame($username, $created->getUserIdentifier());
        self::assertSame($username, $created->getUsername());
        self::assertSame($email, $created->getEmail());
        unset($created);

        // Test email verification right away
        self::assertQueuedEmailCount(1);
        $msg = self::getMailerMessage();
        self::assertEmailHeaderSame($msg, 'To', $email);
        $text = $locale === 'en' ? 'Please confirm your e-mail address by clicking the following link' : 'Veuillez confirmer votre adresse e-mail en cliquant sur le lien suivant';
        self::assertEmailTextBodyContains($msg, $text);
        self::assertEmailHtmlBodyContains($msg, $text);
        $body = $msg->getHtmlBody();
        $expectedUrl = 'http://localhost'.RegistrationController::VERIFY_EMAIL_PATHS[$locale];
        self::assertMatchesRegularExpression(\sprintf('~"%s[^"]+"~', $expectedUrl), $body);
        $url = \trim(\preg_replace(\sprintf('~^.*"(%s[^"]+)".*$~isUu', $expectedUrl), '$1', $body));
        self::assertMatchesRegularExpression(\sprintf('~^%s?.+$~isUu', $expectedUrl), $url);

        // Do the email verification
        $client->getContainer()->get(EntityManagerInterface::class)->close();
        self::ensureKernelShutdown();
        $client = self::createClient();
        $client->request('GET', $url);
        $form = $client->getCrawler()->filter('.login-wrapper form')->form();
        $client->submit($form, [
            'username' => $username,
            'password' => $password,
        ]);

        $user = $repo->findOneBy(['email' => $email]);
        self::assertNotNull($user);
        self::assertTrue($user->isEmailConfirmed());
    }
}
