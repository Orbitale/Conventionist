<?php

namespace App\Tests\Controller\Public;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Tests\TestUtils\GetUser;
use App\Tests\TestUtils\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class NotificationControllerTest extends WebTestCase
{
    use ProvidesLocales;
    use GetUser;

    #[DataProvider('provideLocales')]
    public function testIndexRequiresAuth(string $locale): void
    {
        $client = self::createClient();
        $client->request('GET', '/'.$locale.'/notifications');

        self::assertResponseRedirects();
    }

    #[DataProvider('provideLocales')]
    public function testIndexLoggedIn(string $locale): void
    {
        $client = self::createClient();
        $client->loginUser($this->getUser('visitor'));
        $client->request('GET', '/'.$locale.'/notifications');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Notifications');
    }

    public function testMarkAllAsRead(): void
    {
        $client = self::createClient();
        $user = $this->getUser('visitor');
        $client->loginUser($user);

        $repository = self::getContainer()->get(NotificationRepository::class);
        self::assertGreaterThan(0, $repository->countUnreadForRecipient($user));

        $client->request('POST', '/en/notifications/read-all');
        self::assertResponseRedirects('/en/notifications');

        self::assertSame(0, $repository->countUnreadForRecipient($user));
    }

    public function testMarkOneAsRead(): void
    {
        $client = self::createClient();
        $user = $this->getUser('visitor');
        $client->loginUser($user);

        $repository = self::getContainer()->get(NotificationRepository::class);
        $notifications = $repository->findForRecipient($user);
        /** @var Notification $unread */
        $unread = null;
        foreach ($notifications as $notification) {
            if (!$notification->isRead()) {
                $unread = $notification;
                break;
            }
        }
        self::assertNotNull($unread);

        $client->request('POST', '/en/notifications/'.$unread->getId().'/read');
        self::assertResponseRedirects('/en/notifications');
    }

    public function testMarkOtherUsersNotificationIs404(): void
    {
        $client = self::createClient();
        $visitor = $this->getUser('visitor');
        $repository = self::getContainer()->get(NotificationRepository::class);
        $notifications = $repository->findForRecipient($visitor);
        self::assertNotEmpty($notifications);
        $id = $notifications[0]->getId();

        $client->loginUser($this->getUser('admin'));
        $client->request('POST', '/en/notifications/'.$id.'/read');
        self::assertResponseStatusCodeSame(404);
    }
}
