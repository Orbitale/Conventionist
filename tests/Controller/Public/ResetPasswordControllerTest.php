<?php

namespace App\Tests\Controller\Public;

use App\Controller\Public\AuthController;
use App\Controller\Public\ResetPasswordController;
use App\Tests\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ResetPasswordControllerTest extends WebTestCase
{
    use ProvidesLocales;

    #[DataProvider('provideLocales')]
    public function testRequest(string $locale): void
    {
        // Request email change
        $client = self::createClient();
        $client->followRedirects(false);

        $client->request('GET', ResetPasswordController::RESET_PASSWORD_REQUEST_PATHS[$locale]);
        self::assertResponseIsSuccessful();
        $client->submitForm(
            match ($locale) {
                'fr' => 'Demander à réinitialiser le mot de passe',
                default => 'Send reset password request',
            },
            ['reset_password_request_form[email]' => 'admin@test.localhost']
        );
        self::assertSame('http://localhost'.ResetPasswordController::RESET_PASSWORD_REQUEST_PATHS[$locale], $client->getRequest()->getUri());

        // Check email sent
        self::assertQueuedEmailCount(1);
        $msg = self::getMailerMessage();
        self::assertEmailHeaderSame($msg, 'To', 'admin@test.localhost');
        $expectedMsg = match ($locale) {
            'fr' => 'Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant',
            default => 'To reset your password, please visit the following link',
        };
        self::assertEmailTextBodyContains($msg, $expectedMsg);
        self::assertEmailHtmlBodyContains($msg, $expectedMsg);
        $body = $msg->getTextBody();
        $regex = 'http://localhost'.\str_replace('{token}', '[a-zA-Z0-9_]+', ResetPasswordController::RESET_PASSWORD_RESET_PATHS[$locale]);
        self::assertMatchesRegularExpression(\sprintf('~%s~', $regex), $body);

        // Do the reset from the token
        $url = \trim(\preg_replace(\sprintf('~^.*(%s)\n.*$~isUu', $regex), '$1', $body));
        self::assertMatchesRegularExpression(\sprintf('~^%s$~', $regex), $url);
        $client->request('GET', $url);
        self::assertResponseRedirects(\str_replace('/{token}', '', ResetPasswordController::RESET_PASSWORD_RESET_PATHS[$locale]));
        $client->followRedirect();
        $pwd = '@im*VMfslc0GC@XBNiZ8K6LYMb';
        $client->submitForm(
            match ($locale) {
                'fr' => 'Réinitialiser',
                default => 'Reset password',
            },
            [
                'change_password_form[plainPassword][first]' => $pwd,
                'change_password_form[plainPassword][second]' => $pwd,
            ],
        );
        self::assertResponseRedirects('/'.$locale);

        // Test login with new password to ensure it's been persisted
        $crawler = $client->request('GET', AuthController::LOGIN_PATHS[$locale]);
        $form = $crawler->filter('.login-wrapper form')->form();
        $client->submit($form, [
            'username' => 'admin',
            'password' => $pwd,
        ]);
        self::assertResponseRedirects('/'.$locale.'/admin');
        $crawler = $client->followRedirect();
        self::assertSame('Conventionist', $crawler->filter('#header-logo')->text());
    }
}
