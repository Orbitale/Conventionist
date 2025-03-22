<?php

namespace App\Tests\Twig;

use App\DataFixtures\AttendeeFixture;
use App\DataFixtures\ScheduledActivityFixture;
use App\Entity\ScheduledActivity;
use App\Repository\ScheduledActivityRepository;
use App\Tests\TestUtils\GetUser;
use App\Twig\Extension\ActivityExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ActivityExtensionTest extends KernelTestCase
{
    use GetUser;

    #[DataProvider('provideData')]
    public function testBasicIntegration(string $username, string $activityId, bool $expectedResult): void
    {
        self::bootKernel();
        /** @var ActivityExtension $extension */
        $extension = self::getContainer()->get(ActivityExtension::class);

        self::assertSame('is_user_registered_to_activity', $extension->getFunctions()[0]->getName());

        $user = $this->getUser($username);
        /** @var ScheduledActivity $activity */
        $activity = self::getContainer()->get(ScheduledActivityRepository::class)->find($activityId);

        $result = $extension->isUserRegisteredToActivity($user, $activity);

        self::assertSame($expectedResult, $result);
    }

    public static function provideData(): iterable
    {
        foreach (AttendeeFixture::getStaticData() as $data) {
            $username = \preg_replace('~^user-(.+)$~', '$1', $data['registeredBy']->name);
            $activityId = \preg_replace('~^scheduled-activity-(.+)$~', '$1', $data['scheduledActivity']->name);
            yield $username.' '.$activityId => [
                $username,
                $activityId,
                true,
            ];
        }

        foreach (ScheduledActivityFixture::getStaticData() as $id => $data) {
            yield 'ash '.$id => ['ash', $id, false];
            yield 'admin '.$id => ['admin', $id, false];
        }
    }
}
