<?php

namespace App\Tests\Controller\Public;

use App\Enum\ScheduleActivityState;
use App\Tests\TestUtils\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SessionSearchControllerTest extends WebTestCase
{
    use ProvidesLocales;

    #[DataProvider('provideLocales')]
    public function testUnfilteredListRenders(string $locale): void
    {
        $client = self::createClient();
        $client->request('GET', '/'.$locale.'/sessions');

        self::assertResponseIsSuccessful();
    }

    public function testFilteredByState(): void
    {
        $client = self::createClient();
        $client->request('GET', '/en/sessions', [
            'state' => ScheduleActivityState::ACCEPTED->value,
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testFilteredByQuery(): void
    {
        $client = self::createClient();
        $client->request('GET', '/en/sessions', ['q' => 'dragon']);

        self::assertResponseIsSuccessful();
    }

    public function testInvalidDateIsIgnoredGracefully(): void
    {
        $client = self::createClient();
        $client->request('GET', '/en/sessions', [
            'from' => 'not-a-date',
            'to' => 'also-broken',
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testInvalidStateIsIgnored(): void
    {
        $client = self::createClient();
        $client->request('GET', '/en/sessions', ['state' => 'bogus-state']);

        self::assertResponseIsSuccessful();
    }

    public function testSeatsAvailableFilter(): void
    {
        $client = self::createClient();
        $client->request('GET', '/en/sessions', ['seats' => 'available']);

        self::assertResponseIsSuccessful();
    }
}
