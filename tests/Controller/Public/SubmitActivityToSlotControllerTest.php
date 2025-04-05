<?php

namespace App\Tests\Controller\Public;

use App\Controller\Public\EventController;
use App\Controller\Public\SubmitActivityToSlotController;
use App\DataFixtures\ActivityFixture;
use App\DataFixtures\ScheduledActivityFixture;
use App\DataFixtures\TimeSlotFixture;
use App\DataFixtures\Tools\Ref;
use App\Enum\ScheduleActivityState;
use App\Repository\ActivityRepository;
use App\Repository\ScheduledActivityRepository;
use App\Tests\TestUtils\Assertions\FlashMessageAssertions;
use App\Tests\TestUtils\GetUser;
use App\Tests\TestUtils\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SubmitActivityToSlotControllerTest extends WebTestCase
{
    use GetUser;
    use ProvidesLocales;
    use FlashMessageAssertions;

    #[DataProvider('provideLocales')]
    public function testSlotDoesNotExist(string $locale): void
    {
        $path = \str_replace('{id}', Uuid::v7()->toString(), SubmitActivityToSlotController::PATHS[$locale]);

        $client = self::createClient();
        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(404);
    }

    #[DataProvider('provideLocales')]
    public function testClosedSlot(string $locale): void
    {
        $slotId = \key(TimeSlotFixture::filterData(static fn (array $data) => ($data['open'] ?? true) === false));

        $path = str_replace('{id}', $slotId, SubmitActivityToSlotController::PATHS[$locale]);

        $client = self::createClient();
        $client->request('GET', $path);
        self::assertResponseRedirects(\str_replace('{slug}', 'tdc-2025', EventController::PATHS[$locale]));
        self::assertSessionHasFlashMessage('danger', 'event.error.cannot_submit_activity_to_slot');
    }

    #[DataProvider('provideLocales')]
    public function testSlotWithAcceptedActivity(string $locale): void
    {
        /** @var Ref|null $slotRef */
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

    #[DataProvider('provideLocales')]
    public function testSubmitWithExistingActivity(string $locale): void
    {
        $username = 'visitor';
        $slotId = '11cca5a1-57f5-408c-bb2d-27cd0631fc5c';
        $activityId = '6d443a40-6c42-4ced-bb1c-a285e415a768';

        $path = str_replace('{id}', $slotId, SubmitActivityToSlotController::PATHS[$locale]);

        $client = self::createClient();
        $user = $this->getUser($username);
        $client->loginUser($user);

        // Open the page
        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(200);

        // Submit the form
        $form = $client->getCrawler()->filter('form[name="submit_activity_to_slot"]')->form();
        self::assertSame($user->getEmail(), $client->getCrawler()->filter('#submit_activity_to_slot_email')->attr('value'));
        $client->submit($form, [
            'submit_activity_to_slot[selectedActivity]' => $activityId,
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextSame('.alert.alert-danger', self::getContainer()->get(TranslatorInterface::class)->trans('event.error.already_submitted_activity', locale: $locale));
    }


    #[DataProvider('provideLocales')]
    public function testSubmitAsInexistentUser(string $locale): void
    {
        $client = self::createClient();

        $slotId = 'ed52861f-3cfd-47df-ac1d-ffaedf6910e8';
        $path = str_replace('{id}', $slotId, SubmitActivityToSlotController::PATHS[$locale]);

        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(200);

        $form = $client->getCrawler()->filter('form[name="submit_activity_to_slot"]')->form();
        $client->submit($form, [
            'submit_activity_to_slot[email]' => $userEmail = 'yet_inexistent_email@test.localhost',
            'submit_activity_to_slot[newActivity][name]' => $activityName = 'Newly created activity',
            'submit_activity_to_slot[newActivity][maxNumberOfParticipants]' => 2,
            'submit_activity_to_slot[newActivity][description]' => 'Lorem ipsum dolor sit amet',
        ]);

        self::assertResponseRedirects(\str_replace('{slug}', 'tdc-2025', EventController::PATHS[$locale]));
        $user = $this->getUser($userEmail);
        self::assertFalse($user->isEmailConfirmed());
        self::assertSame(\preg_replace('~@.*$~sUu', '', $userEmail), $user->getUserIdentifier());
        self::assertSame($locale, $user->getLocale());

        $activity = self::getContainer()->get(ActivityRepository::class)->findOneBy(['name' => $activityName]);
        self::assertNotNull($activity);
        self::assertTrue($user->isOwnerOf($activity));

        $scheduledActivity = self::getContainer()->get(ScheduledActivityRepository::class)->findOneBy(['activity' => $activity]);
        self::assertNotNull($scheduledActivity);
        self::assertTrue($scheduledActivity->getSubmittedBy()->isSameAs($user));
        self::assertSame(ScheduleActivityState::CREATED, $scheduledActivity->getState());
    }
}
