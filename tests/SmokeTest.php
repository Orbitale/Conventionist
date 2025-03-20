<?php

namespace App\Tests;

use App\Tests\TestUtils\GetUser;
use Pierstoval\SmokeTesting\SmokeTestStaticRoutes;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

final class SmokeTest extends SmokeTestStaticRoutes
{
    use GetUser;

    protected function beforeRequest(KernelBrowser $client, string $routeName, string $routePath): void
    {
        if (\str_starts_with('/admin', $routePath)) {
            $client->loginUser($this->getUser());
        }

        if ($routeName === 'login') {
            self::markTestSkipped('Do not test login globally.');
        }
    }
}
