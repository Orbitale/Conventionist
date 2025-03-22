<?php

namespace App\Tests\Controller\Public;

use App\Controller\Public\EventController;
use App\Controller\Public\SubmitActivityToSlotController;
use App\DataFixtures\ScheduledActivityFixture;
use App\DataFixtures\TimeSlotFixture;
use App\DataFixtures\Tools\Ref;
use App\Enum\ScheduleActivityState;
use App\Tests\TestUtils\Assertions\FlashMessageAssertions;
use App\Tests\TestUtils\GetUser;
use App\Tests\TestUtils\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

final class SubmitActivityToSlotControllerTest extends WebTestCase
{
    use GetUser;
    use ProvidesLocales;
    use FlashMessageAssertions;

    #[DataProvider('provideLocales')]
    public function testSlotDoesNotExist(string $locale): void
    {
        $path = str_replace(
            '{id}',
            Uuid::v7()->toString(),
            SubmitActivityToSlotController::PATHS[$locale],
        );

        $client = self::createClient();
        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(404);
    }

    #[DataProvider('provideLocales')]
    public function testClosedSlot(string $locale): void
    {
        $data = TimeSlotFixture::filterData(static fn (array $data) => ($data['open'] ?? true) === false);
        self::assertNotEmpty($data);
        $slotId = \key($data);
        self::assertNotNull($slotId);

        $path = str_replace('{id}', $slotId, SubmitActivityToSlotController::PATHS[$locale]);

        $client = self::createClient();
        $client->request('GET', $path);
        self::assertResponseRedirects(\str_replace('{slug}', 'tdc-2025', EventController::PATHS[$locale]));
        self::assertSessionHasFlashMessage('danger', 'event.error.cannot_submit_activity_to_slot');
    }

    #[DataProvider('provideLocales')]
    public function testSlotWithAcceptedActivity(string $locale): void
    {
        /** @var null|Ref $slotRef */
        $data = ScheduledActivityFixture::filterData(static fn (array $data) => $data['state'] === ScheduleActivityState::ACCEPTED);
        $slotRef = \reset($data)['timeSlot'] ?? null;
        self::assertNotNull($slotRef);
        $slotId = \preg_replace('~^timeslot-(.+)~', '$1', $slotRef->name);

        $path = str_replace('{id}', $slotId, SubmitActivityToSlotController::PATHS[$locale]);

        $client = self::createClient();
        $client->request('GET', $path);
        self::assertResponseRedirects(\str_replace('{slug}', 'tdc-2025', EventController::PATHS[$locale]));
        self::assertSessionHasFlashMessage('danger', 'event.error.cannot_submit_activity_to_slot');
    }
}
