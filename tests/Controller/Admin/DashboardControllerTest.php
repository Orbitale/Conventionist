<?php

namespace App\Tests\Controller\Admin;

use App\Tests\GetUser;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DashboardControllerTest extends WebTestCase
{
    use GetUser;

    #[TestWith(['admin'])]
    #[TestWith(['visitor'])]
    #[TestWith(['ash'])]
    public function testAdminPermissions(string $username): void
    {
        $client = self::createClient();

        $client->loginUser($this->getUser($username));
        $client->request('GET', '/fr/admin');
        self::assertResponseIsSuccessful();
        $links = $client->getCrawler()->filter('#main-menu a')->each(fn ($node) => $node->text());
        $expectedLinks = [
            'Retour au site',
            'Tableau de bord',
            'Évènements',
            'Calendrier',
            'Lieux d\'accueil',
            'Étages',
            'Salles',
            'Stands',
            'Activités',
            'Créneaux horaires',
            'Programmation',
        ];
        if ($username === 'admin') {
            $expectedLinks[] = 'Utilisateurs/utilisatrices';
        }
        self::assertSame($expectedLinks, $links);
    }
}
