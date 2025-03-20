<?php

namespace App\Tests\Controller\Public;

use App\Controller\Public\ActivityAttendeeRegisterController;
use App\Controller\Public\EventController;
use App\DataFixtures\ScheduledActivityFixture;
use App\Enum\ScheduleActivityState;
use App\Repository\AttendeeRepository;
use App\Tests\TestUtils\Assertions\FlashMessageAssertions;
use App\Tests\TestUtils\GetUser;
use App\Tests\TestUtils\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class ActivityAttendeeRegisterControllerTest extends WebTestCase
{
    use GetUser;
    use ProvidesLocales;
    use FlashMessageAssertions;

    #[DataProvider('provideLocales')]
    public function testActivityDoesNotExist(string $locale): void
    {
        $path = str_replace('{id}', Uuid::v7()->toString(), ActivityAttendeeRegisterController::PATHS[$locale]);

        $client = static::createClient();
        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(404);
    }

    #[DataProvider('provideLocales')]
    public function testCannotRegisterActivity(string $locale): void
    {
        $activityId = \key(ScheduledActivityFixture::filterData(static function (array $data) {
            return $data['activity']->name === 'activity-Activité de jeu'
                && $data['state'] === ScheduleActivityState::CREATED
                && $data['submittedBy']->name === 'user-admin';
        }));

        $path = str_replace('{id}', $activityId, ActivityAttendeeRegisterController::PATHS[$locale]);

        $client = static::createClient();
        $client->request('GET', $path);
        self::assertResponseRedirects(\str_replace('{slug}', 'tdc-2025', EventController::PATHS[$locale]));
        self::assertSessionHasFlashMessage('danger', 'event.error.cannot_register_to_activity');
    }

    #[DataProvider('provideLocales')]
    public function testAlreadyRegisteredToActivity(string $locale): void
    {
        $activityId = \key(ScheduledActivityFixture::filterData(static function (array $data) {
            return $data['activity']->name === 'activity-Visitor activity'
                && $data['state'] === ScheduleActivityState::ACCEPTED
                && $data['submittedBy']->name === 'user-visitor';
        }));

        $path = str_replace('{id}', $activityId, ActivityAttendeeRegisterController::PATHS[$locale]);

        $client = static::createClient();
        $client->loginUser($this->getUser('visitor'));
        $client->request('GET', $path);
        self::assertResponseRedirects(\str_replace('{slug}', 'tdc-2025', EventController::PATHS[$locale]));
        self::assertSessionHasFlashMessage('danger', 'event.error.already_registered_to_activity');
    }

    #[DataProvider('provideLocales')]
    public function testUnauthenticatedWorkingIndex(string $locale): void
    {
        $activityId = \key(ScheduledActivityFixture::filterData(static function (array $data) {
            return $data['activity']->name === 'activity-Visitor activity'
                && $data['state'] === ScheduleActivityState::ACCEPTED
                && $data['submittedBy']->name === 'user-visitor';
        }));

        $path = str_replace('{id}', $activityId, ActivityAttendeeRegisterController::PATHS[$locale]);

        $client = static::createClient();
        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(200);
    }

    #[DataProvider('provideUsers')]
    public function testAuthenticatedWorkingIndex(string $locale, string $username): void
    {
        $activityId = \key(ScheduledActivityFixture::filterData(static function (array $data) {
            return $data['activity']->name === 'activity-Concert'
                && $data['state'] === ScheduleActivityState::ACCEPTED
                && $data['submittedBy']->name === 'user-visitor';
        }));

        $path = str_replace('{id}', $activityId, ActivityAttendeeRegisterController::PATHS[$locale]);

        $client = static::createClient();
        $client->loginUser($this->getUser($username));
        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(200);
    }

    public static function provideUsers(): iterable
    {
        foreach (self::provideLocales() as $locale => $_) {
            yield $locale.' ash' => [$locale, 'ash'];
            yield $locale.' admin' => [$locale, 'admin'];
            yield $locale.' visitor' => [$locale, 'visitor'];
        }
    }
}
