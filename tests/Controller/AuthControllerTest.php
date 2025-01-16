<?php

namespace App\Tests\Controller;

use App\Tests\GetUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    use GetUser;

    public function testLogin(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('.login-wrapper form')->form();
        $client->submit($form, [
            'username' => 'admin',
            'password' => 'admin',
        ]);
        self::assertResponseRedirects('/admin');
        $crawler = $client->followRedirect();
        self::assertSame('Conventionist', $crawler->filter('#header-logo')->text());
    }

    public function testFailedLogin(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('.login-wrapper form')->form();
        $client->submit($form, [
            'username' => 'inexistent_data',
            'password' => 'inexistent_data',
        ]);
        self::assertResponseRedirects('/login');
        $crawler = $client->followRedirect();
        self::assertSame('Invalid credentials.', $crawler->filter('.alert.alert-danger')->text());
    }

    public function testLogout(): void
    {
        $client = self::createClient();

        $client->loginUser($this->getUser());

        // Checked properly logged in
        $crawler = $client->request('GET', '/admin');
        self::assertSame('admin (admin@test.localhost)', $crawler->filter('.navbar-custom-menu .user-name')->text());

        // Perform logout
        $client->request('GET', '/logout');
        self::assertResponseRedirects('/');

        // Make sure logout prevents access to logged-in-only page
        $client->request('GET', '/admin');
        self::assertResponseRedirects('/login');
    }
}
