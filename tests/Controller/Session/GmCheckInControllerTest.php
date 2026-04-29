<?php

namespace App\Tests\Controller\Session;

use App\Entity\ScheduledActivity;
use App\Enum\ScheduleActivityState;
use App\Repository\ScheduledActivityRepository;
use App\Tests\TestUtils\GetUser;
use App\Tests\TestUtils\ProvidesLocales;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GmCheckInControllerTest extends WebTestCase
{
    use ProvidesLocales;
    use GetUser;

    /**
     * Fixture scheduled activity submitted by the "visitor" user.
     */
    private const string VISITOR_SESSION_ID = 'f166e2cf-0f87-4aff-90d8-1a7466480238';

    #[DataProvider('provideLocales')]
    public function testAnonymousIsRedirected(string $locale): void
    {
        $client = self::createClient();
        $client->request('POST', '/'.$locale.'/sessions/'.self::VISITOR_SESSION_ID.'/check-in');

        self::assertResponseRedirects();
    }

    public function testNonOwnerIsForbidden(): void
    {
        $client = self::createClient();
        $client->loginUser($this->getUser('unvalidated'));
        $client->request('POST', '/en/sessions/'.self::VISITOR_SESSION_ID.'/check-in');

        self::assertResponseStatusCodeSame(403);
    }

    public function testOwnerCanCheckIn(): void
    {
        $client = self::createClient();
        $client->loginUser($this->getUser('visitor'));

        // Put the session into AWAITING_GM so the transition can fire.
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $repo = self::getContainer()->get(ScheduledActivityRepository::class);
        /** @var ScheduledActivity $session */
        $session = $repo->find(self::VISITOR_SESSION_ID);
        $session->setState(ScheduleActivityState::AWAITING_GM);
        $session->setGmCheckedInAt(null);
        $em->flush();

        $client->request('POST', '/en/sessions/'.self::VISITOR_SESSION_ID.'/check-in');

        self::assertResponseRedirects();

        $em->clear();
        /** @var ScheduledActivity $refreshed */
        $refreshed = $repo->find(self::VISITOR_SESSION_ID);
        self::assertTrue($refreshed->hasGmCheckedIn());
        self::assertSame(ScheduleActivityState::ACCEPTED, $refreshed->getState());
    }
}
