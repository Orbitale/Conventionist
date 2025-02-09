<?php

namespace App\Tests\Controller\Admin;

use App\Tests\GetUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    use GetUser;

    public function testAdminPermissions(): void
    {
        $client = self::createClient();

        $client->loginUser($this->getUser('admin'));
        $client->request("GET", '/admin');
        self::assertResponseIsSuccessful();
        $links = $client->getCrawler()->filter('#main-menu a')->each(fn ($node) => $node->text());
        self::assertSame([
            'Tableau de bord',
            'Évènements',
            'Calendrier',
            'Lieux d\'accueil',
            'Étages',
            'Salles',
            'Tables',
            'Animations',
            'Programmation',
            'Créneaux horaires',
        ], $links);
    }

    public function testVisitorPermissions(): void
    {
        $client = self::createClient();

        $client->loginUser($this->getUser('visitor'));
        $client->request("GET", '/admin');
        self::assertResponseIsSuccessful();
        $links = $client->getCrawler()->filter('#main-menu a')->each(fn ($node) => $node->text());
        self::assertSame([
            'Tableau de bord',
            'Évènements',
            'Calendrier',
            'Animations',
            'Programmation',
        ], $links);
    }

    public function testConferenceOrganizerPermissions(): void
    {
        $client = self::createClient();

        $client->loginUser($this->getUser('conference_organizer'));
        $client->request("GET", '/admin');
        self::assertResponseIsSuccessful();
        $links = $client->getCrawler()->filter('#main-menu a')->each(fn ($node) => $node->text());
        self::assertSame([
            'Tableau de bord',
            'Évènements',
            'Calendrier',
            'Animations',
            'Programmation',
            'Créneaux horaires',
        ], $links);

    }

    public function testVenueManagerPermissions(): void
    {
        $client = self::createClient();

        $client->loginUser($this->getUser('venue_manager'));
        $client->request("GET", '/admin');
        self::assertResponseIsSuccessful();
        $links = $client->getCrawler()->filter('#main-menu a')->each(fn ($node) => $node->text());
        self::assertSame([
            'Tableau de bord',
            'Lieux d\'accueil',
            'Étages',
            'Salles',
            'Tables',
        ], $links);
    }
}
