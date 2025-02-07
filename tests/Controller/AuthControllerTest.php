<?php

namespace App\Tests\Controller;

use App\Tests\GetUser;
use App\Tests\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    use ProvidesLocales;
    use GetUser;

    #[DataProvider('provideLocales')]
    public function testLogin(string $locale): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', \sprintf("/%s/login", $locale));
        $form = $crawler->filter('.login-wrapper form')->form();
        $client->submit($form, [
            'username' => 'admin',
            'password' => 'admin',
        ]);
        self::assertResponseRedirects('/admin');
        $crawler = $client->followRedirect();
        self::assertSame('Conventionist', $crawler->filter('#header-logo')->text());
    }

    #[DataProvider('provideLocales')]
    public function testFailedLogin(string $locale): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', \sprintf("/%s/login", $locale));
        $form = $crawler->filter('.login-wrapper form')->form();
        $client->submit($form, [
            'username' => 'inexistent_data',
            'password' => 'inexistent_data',
        ]);
        self::assertResponseRedirects(\sprintf("/%s/login", $locale));
        $crawler = $client->followRedirect();
        self::assertSame('Invalid credentials.', $crawler->filter('.alert.alert-danger')->text());
    }

    #[DataProvider('provideLocales')]
    public function testLogout(string $locale): void
    {
        $client = self::createClient();

        $client->loginUser($this->getUser());

        // Checked properly logged in
        $crawler = $client->request('GET', '/admin');
        self::assertSame('admin (admin@test.localhost)', $crawler->filter('.navbar-custom-menu .user-name')->text());

        // Perform logout
        $client->request('GET', \sprintf("/%s/logout", $locale));
        self::assertResponseRedirects('/');

        // Make sure logout prevents access to logged-in-only page
        $client->request('GET', '/admin');
        self::assertResponseRedirects(\sprintf("/%s/login", $locale));
    }
}
