<?php

namespace App\Tests\Controller\Admin;

use App\Tests\TestUtils\GetUser;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CalendarControllerTest extends WebTestCase
{
    use GetUser;

    #[TestWith(['admin', ['TDC 2025', 'Custom event']])]
    #[TestWith(['ash', ['TDC 2025']])]
    #[TestWith(['visitor', ['Custom event']])]
    public function testCalendarEventsList(string $username, array $expectedEvents): void
    {
        $client = self::createClient();

        $client->loginUser($this->getUser($username));
        $client->request('GET', '/en/admin/calendar');
        self::assertResponseIsSuccessful();
        $links = $client->getCrawler()->filter('#main .btn-group a')->each(fn ($node) => $node->text());
        \array_pop($links); // Remove "Create new"
        self::assertSame($expectedEvents, $links);
    }
}
