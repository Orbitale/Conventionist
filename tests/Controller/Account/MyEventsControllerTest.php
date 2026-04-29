<?php

namespace App\Tests\Controller\Account;

use App\Tests\TestUtils\GetUser;
use App\Tests\TestUtils\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MyEventsControllerTest extends WebTestCase
{
    use ProvidesLocales;
    use GetUser;

    #[DataProvider('provideLocales')]
    public function testMyEventsAnonymousRedirects(string $locale): void
    {
        $client = self::createClient();
        $client->request('GET', '/'.$locale.'/my/events');

        self::assertResponseRedirects();
    }

    #[DataProvider('provideLocales')]
    public function testMyAgendaAnonymousRedirects(string $locale): void
    {
        $client = self::createClient();
        $client->request('GET', '/'.$locale.'/my/agenda');

        self::assertResponseRedirects();
    }

    #[DataProvider('provideLocales')]
    public function testMyEventsAuthenticated(string $locale): void
    {
        $client = self::createClient();
        $client->loginUser($this->getUser('visitor'));
        $client->request('GET', '/'.$locale.'/my/events');

        self::assertResponseIsSuccessful();
    }

    #[DataProvider('provideLocales')]
    public function testMyAgendaAuthenticated(string $locale): void
    {
        $client = self::createClient();
        $client->loginUser($this->getUser('visitor'));
        $client->request('GET', '/'.$locale.'/my/agenda');

        self::assertResponseIsSuccessful();
    }

    public function testMyAgendaWithDateOverrides(): void
    {
        $client = self::createClient();
        $client->loginUser($this->getUser('visitor'));
        $client->request('GET', '/en/my/agenda?from=2026-01-01&to=2026-12-31');

        self::assertResponseIsSuccessful();
    }
}
