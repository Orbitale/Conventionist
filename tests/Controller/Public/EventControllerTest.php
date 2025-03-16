<?php

namespace App\Tests\Controller\Public;

use App\Controller\Public\EventController;
use App\Tests\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
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
        self::assertSame(1, $activities->count());
        $activity = $activities->first();
        self::assertSame('Visitor activity 04:00 - 05:00', $activity->filter('.card-header h4')->text());
    }
}
