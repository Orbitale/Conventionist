<?php

namespace App\Tests\TestUtils\Assertions;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

trait FlashMessageAssertions
{
    public static function assertSessionHasFlashMessage(string $messageType, string $message = ''): void
    {
        if (!\is_a(static::class, WebTestCase::class, true)) {
            throw new \RuntimeException(\sprintf('Trait "%s" can only be used in a test that extends "%s".', FlashMessageAssertions::class, WebTestCase::class));
        }

        static::assertThat(self::getRequest(), new SessionHasFlashMessage($messageType, $message));
    }

    /**
     * This is a copy-paste of the `getRequest` method from the `BrowserKitAssertionsTrait` trait.
     *
     * @see \Symfony\Bundle\FrameworkBundle\Test\BrowserKitAssertionsTrait::getRequest
     */
    private static function getRequest(): Request
    {
        if (!$request = self::getClient()->getRequest()) {
            static::fail('A client must have an HTTP Request to make assertions. Did you forget to make an HTTP request?');
        }

        return $request;
    }
}
