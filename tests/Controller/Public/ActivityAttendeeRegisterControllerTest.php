<?php

namespace App\Tests\Controller\Public;

use App\Controller\Public\ActivityAttendeeRegisterController;
use App\Controller\Public\EventController;
use App\DataFixtures\ScheduledActivityFixture;
use App\DataFixtures\UserFixture;
use App\Entity\Attendee;
use App\Enum\ScheduleActivityState;
use App\Repository\AttendeeRepository;
use App\Tests\TestUtils\Assertions\FlashMessageAssertions;
use App\Tests\TestUtils\GetUser;
use App\Tests\TestUtils\ProvidesLocales;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    public static function provideUsers(): iterable
    {
        foreach (self::provideLocales() as $locale => $_) {
            yield $locale.' ash' => [$locale, 'ash'];
            yield $locale.' admin' => [$locale, 'admin'];
            yield $locale.' visitor' => [$locale, 'visitor'];
        }
    }

    #[DataProvider('provideUsers')]
    public function testLoginLinkRedirectsToForm(string $locale, string $username): void
    {
        $activityId = \key(ScheduledActivityFixture::filterData(static function (array $data) {
            return $data['activity']->name === 'activity-Concert'
                && $data['state'] === ScheduleActivityState::ACCEPTED
                && $data['submittedBy']->name === 'user-visitor';
        }));

        $path = str_replace('{id}', $activityId, ActivityAttendeeRegisterController::PATHS[$locale]);

        $client = static::createClient();
        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(200);

        $link = $client->getCrawler()->filter('#register_as_activity_attendee a')->link();
        $client->click($link);
        self::assertResponseStatusCodeSame(200);

        $form = $client->getCrawler()->filter('form[method="post"]')->form();
        $client->submit($form, [
            'username' => $username,
            'password' => $username,
        ]);
        self::assertResponseRedirects($path);
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

    #[DataProvider('provideUsers')]
    public function testAuthenticatedSubmitSuccessfully(string $locale, string $username): void
    {
        $activityId = \key(ScheduledActivityFixture::filterData(static function (array $data) {
            return $data['activity']->name === 'activity-Concert'
                && $data['state'] === ScheduleActivityState::ACCEPTED
                && $data['submittedBy']->name === 'user-visitor';
        }));

        $path = str_replace('{id}', $activityId, ActivityAttendeeRegisterController::PATHS[$locale]);

        $client = static::createClient();
        $user = $this->getUser($username);
        $client->loginUser($user);

        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(200);

        $form = $client->getCrawler()->filter('form[name="register_as_activity_attendee"]')->form();
        $client->submit($form, [
            //'register_as_activity_attendee[email]' => '',
            'register_as_activity_attendee[name]' => 'Somebody that I used to know',
            'register_as_activity_attendee[numberOfAttendees]' => 5,
        ]);

        self::assertResponseRedirects(\str_replace('{slug}', 'tdc-2025', EventController::PATHS[$locale]));
        self::assertSessionHasFlashMessage('success', 'event.activity.register_as_attendee.success');

        /** @var null|Attendee $attendee */
        $attendee = self::getContainer()->get(AttendeeRepository::class)->findOneBy([
            'registeredBy' => $user,
            'scheduledActivity' => $activityId,
        ]);
        self::assertNotNull($attendee);
        self::assertTrue($attendee->getRegisteredBy()->isSameAs($user));
        self::assertSame($activityId, $attendee->getScheduledActivity()->getId());
        self::assertSame(5, $attendee->getNumberOfAttendees());
        self::assertSame('Somebody that I used to know', $attendee->getName());
    }

    #[DataProvider('provideLocales')]
    public function testNonAuthenticatedSubmitSuccessfullyWithUnknownEmail(string $locale): void
    {
        $activityId = \key(ScheduledActivityFixture::filterData(static function (array $data) {
            return $data['activity']->name === 'activity-Concert'
                && $data['state'] === ScheduleActivityState::ACCEPTED
                && $data['submittedBy']->name === 'user-visitor';
        }));

        $path = str_replace('{id}', $activityId, ActivityAttendeeRegisterController::PATHS[$locale]);

        $client = static::createClient();

        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(200);

        $form = $client->getCrawler()->filter('form[name="register_as_activity_attendee"]')->form();
        $client->submit($form, [
            'register_as_activity_attendee[email]' => 'test@test.localhost',
            'register_as_activity_attendee[name]' => 'Somebody that I used to know',
            'register_as_activity_attendee[numberOfAttendees]' => 5,
        ]);

        self::assertResponseRedirects(\str_replace('{slug}', 'tdc-2025', EventController::PATHS[$locale]));
        self::assertSessionHasFlashMessage('success', 'event.activity.register_as_attendee.success');

        $user = $this->getUser('test@test.localhost');

        /** @var null|Attendee $attendee */
        $attendee = self::getContainer()->get(AttendeeRepository::class)->findOneBy([
            'registeredBy' => $user,
            'scheduledActivity' => $activityId,
        ]);
        self::assertNotNull($attendee);
        self::assertTrue($attendee->getRegisteredBy()->isSameAs($user));
        self::assertSame($activityId, $attendee->getScheduledActivity()->getId());
        self::assertSame(5, $attendee->getNumberOfAttendees());
        self::assertSame('Somebody that I used to know', $attendee->getName());
    }

    #[DataProvider('provideUsers')]
    public function testNonAuthenticatedSubmitSuccessfullyExistingEmail(string $locale, string $username): void
    {
        $activityId = \key(ScheduledActivityFixture::filterData(static function (array $data) {
            return $data['activity']->name === 'activity-Concert'
                && $data['state'] === ScheduleActivityState::ACCEPTED
                && $data['submittedBy']->name === 'user-visitor';
        }));

        $path = str_replace('{id}', $activityId, ActivityAttendeeRegisterController::PATHS[$locale]);

        $client = static::createClient();

        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(200);

        $user = $this->getUser($username);

        $form = $client->getCrawler()->filter('form[name="register_as_activity_attendee"]')->form();
        $client->submit($form, [
            'register_as_activity_attendee[email]' => $user->getEmail(),
            'register_as_activity_attendee[name]' => 'Somebody that I used to know',
            'register_as_activity_attendee[numberOfAttendees]' => 5,
        ]);

        self::assertResponseRedirects(\str_replace('{slug}', 'tdc-2025', EventController::PATHS[$locale]));
        self::assertSessionHasFlashMessage('success', 'event.activity.register_as_attendee.success');

        /** @var null|Attendee $attendee */
        $attendee = self::getContainer()->get(AttendeeRepository::class)->findOneBy([
            'registeredBy' => $user,
            'scheduledActivity' => $activityId,
        ]);
        self::assertNotNull($attendee);
        self::assertTrue($attendee->getRegisteredBy()->isSameAs($user));
        self::assertSame($activityId, $attendee->getScheduledActivity()->getId());
        self::assertSame(5, $attendee->getNumberOfAttendees());
        self::assertSame('Somebody that I used to know', $attendee->getName());
    }

    #[DataProvider('provideLocales')]
    public function testNonAuthenticatedSubmitFailsIfAlreadyRegistered(string $locale): void
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

        $user = $this->getUser('visitor');

        $form = $client->getCrawler()->filter('form[name="register_as_activity_attendee"]')->form();
        $client->submit($form, [
            'register_as_activity_attendee[email]' => $user->getEmail(),
            'register_as_activity_attendee[name]' => 'Somebody that I used to know',
            'register_as_activity_attendee[numberOfAttendees]' => 5,
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextSame('.alert.alert-danger', self::getContainer()->get(TranslatorInterface::class)->trans('event.error.already_registered_to_activity', locale: $locale));
    }
}
