<?php

namespace App\Tests\Controller;

use App\Controller\RegistrationController;
use App\Repository\UserRepository;
use App\Tests\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RegistrationControllerTest extends WebTestCase
{
    use ProvidesLocales;

    #[DataProvider('provideLocales')]
    public function testIndexWithLocale(string $locale): void
    {
        $client = static::createClient(server: ['HTTP_ACCEPT_LANGUAGE' => $locale]);
        $client->request('GET', RegistrationController::REGISTER_PATHS[$locale]);

        $repo = $client->getContainer()->get(UserRepository::class);

        $users = $repo->findAll();

        $translator = $client->getContainer()->get(TranslatorInterface::class);

        $registerText = $translator->trans('register', locale: $locale);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', $registerText);

        $form = $client->getCrawler()->selectButton($registerText)->form([
            'registration_form[username]' => 'new-user',
            'registration_form[email]' => 'new-user@test.localhost',
            'registration_form[plainPassword]' => 'new-user-password',
        ]);
        $client->submit($form);
        self::assertResponseStatusCodeSame(302);

        /** @var UserRepository $repo */
        $repo = $client->getContainer()->get(UserRepository::class);
        $newUsers = $repo->findAll();

        $created = \array_values(\array_diff($newUsers, $users));
        self::assertCount(1, $created);

        $created = $created[0];
        self::assertSame('new-user', $created->getUserIdentifier());
        self::assertSame('new-user', $created->getUsername());
        self::assertSame('new-user@test.localhost', $created->getEmail());
    }
}
