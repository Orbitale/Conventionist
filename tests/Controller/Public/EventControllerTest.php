<?php

namespace App\Tests\Controller\Public;

use App\Controller\Public\EventController;
use App\Tests\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class EventControllerTest extends WebTestCase
{
    use ProvidesLocales;

    #[DataProvider('provideLocales')]
    public function testIndexistentEvent(string $locale): void
    {
        $slug = 'inexistent-slug';

        $client = self::createClient();
        $client->request('GET', \str_replace('{slug}', $slug, EventController::PATHS[$locale]));

        self::assertResponseStatusCodeSame(404);
    }

    public function testEventView(): void
    {
        $locale = 'fr';
        $slug = 'tdc-2025';

        $client = self::createClient();
        $crawler = $client->request('GET', \str_replace('{slug}', $slug, EventController::PATHS[$locale]));

        $activities = $crawler->filter('.activity');
        self::assertSame(8, $activities->count());
        $visitorActivity = $activities->eq(3);
        self::assertSame('Visitor activity 04:00 - 05:00', $visitorActivity->filter('.activity-title')->text());
        $concertActivity = $activities->eq(5);
        self::assertSame('Concert 03:00 - 07:00', $concertActivity->filter('.activity-title')->text());
    }
}
