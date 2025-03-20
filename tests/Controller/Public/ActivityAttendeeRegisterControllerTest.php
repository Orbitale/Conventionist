<?php

namespace App\Tests\Controller\Public;

use App\Controller\Public\ActivityAttendeeRegisterController;
use App\DataFixtures\ScheduledActivityFixture;
use App\Enum\ScheduleActivityState;
use App\Tests\TestUtils\Assertions\FlashMessageAssertions;
use App\Tests\TestUtils\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActivityAttendeeRegisterControllerTest extends WebTestCase
{
    use ProvidesLocales;
    use FlashMessageAssertions;

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
        self::assertResponseStatusCodeSame(302);
        self::assertSessionHasFlashMessage('danger', 'event.error.cannot_register_to_activity');
    }

    #[DataProvider('provideLocales')]
    public function testWorkingIndex(string $locale): void
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
}
