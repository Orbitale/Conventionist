<?php

namespace App\Tests\Controller;

use App\Tests\GetUser;
use App\Tests\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class IndexControllerTest extends WebTestCase
{
    use ProvidesLocales;
    use GetUser;

    public function testRoot(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseRedirects('/en');
    }

    #[DataProvider('provideLocales')]
    public function testRootWithLocale(string $locale): void
    {
        $client = static::createClient(server: ['HTTP_ACCEPT_LANGUAGE' => $locale]);
        $client->request('GET', '/');

        self::assertResponseRedirects('/'.$locale);
    }

    #[DataProvider('provideLocales')]
    public function testIndexWithLocale(string $locale): void
    {
        $client = static::createClient(server: ['HTTP_ACCEPT_LANGUAGE' => $locale]);
        $client->request('GET', '/'.$locale);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Conventionist');
    }

    #[DataProvider('provideLocales')]
    public function testIndexLoggedIn(string $locale): void
    {
        $client = static::createClient(server: ['HTTP_ACCEPT_LANGUAGE' => $locale]);
        $client->loginUser($this->getUser());
        $client->request('GET', '/'.$locale);

        self::assertResponseRedirects('/admin');
    }
}
