<?php

namespace App\Tests\Controller;

use App\Controller\AuthController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResetPasswordControllerTest extends WebTestCase
{
    public function testRequest(): void
    {
        // Request email change
        $client = static::createClient();
        $client->followRedirects(false);

        $client->request('GET', '/password/request-reset');
        self::assertResponseIsSuccessful();
        $client->submitForm(
            'Send reset password request',
            ['reset_password_request_form[email]' => 'admin@test.localhost']
        );
        self::assertSame('http://localhost/password/request-reset', $client->getRequest()->getUri());

        // Check email sent
        self::assertQueuedEmailCount(1);
        $msg = self::getMailerMessage();
        self::assertEmailHeaderSame($msg, 'To', 'admin@test.localhost');
        self::assertEmailTextBodyContains($msg, 'To reset your password, please visit the following link');
        self::assertEmailHtmlBodyContains($msg, 'To reset your password, please visit the following link');
        $body = $msg->getTextBody();
        $regex = 'http://localhost/password/reset/[a-zA-Z0-9_]+';
        self::assertMatchesRegularExpression(\sprintf('~%s~', $regex), $body);

        // Do the reset from the token
        $url = \trim(\preg_replace(\sprintf('~^.*(%s)\n.*$~isUu', $regex), '$1', $body));
        self::assertMatchesRegularExpression(\sprintf('~^%s$~', $regex), $url);
        $client->request('GET', $url);
        self::assertResponseRedirects('/password/reset');
        $client->followRedirect();
        $pwd = '@im*VMfslc0GC@XBNiZ8K6LYMb';
        $client->submitForm(
            'Reset password',
            [
                'change_password_form[plainPassword][first]' => $pwd,
                'change_password_form[plainPassword][second]' => $pwd,
            ],
        );
        self::assertResponseRedirects('/en');

        // Test login with new password to ensure it's been persisted
        $crawler = $client->request('GET', AuthController::LOGIN_PATHS['en']);
        $form = $crawler->filter('.login-wrapper form')->form();
        $client->submit($form, [
            'username' => 'admin',
            'password' => $pwd,
        ]);
        self::assertResponseRedirects('/admin');
        $crawler = $client->followRedirect();
        self::assertSame('Conventionist', $crawler->filter('#header-logo')->text());

    }
}
