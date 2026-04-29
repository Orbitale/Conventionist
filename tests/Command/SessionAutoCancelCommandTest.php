<?php

namespace App\Tests\Command;

use App\Command\SessionAutoCancelCommand;
use App\Entity\ScheduledActivity;
use App\Enum\ScheduleActivityState;
use App\Repository\ScheduledActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class SessionAutoCancelCommandTest extends TestCase
{
    public function testDryRunDoesNotFlushOrTransition(): void
    {
        $session = $this->createMock(ScheduledActivity::class);
        $session->expects(self::never())->method('setState');

        $repo = $this->createMock(ScheduledActivityRepository::class);
        $repo->expects(self::once())
            ->method('findStaleAwaitingGm')
            ->willReturn([$session]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $tester = $this->buildTester($repo, $em);
        $tester->execute(['--dry-run' => true]);

        self::assertSame(0, $tester->getStatusCode());
        self::assertStringContainsString('dry-run', $tester->getDisplay());
    }

    public function testNormalRunTransitionsAndFlushes(): void
    {
        $session1 = $this->createMock(ScheduledActivity::class);
        $session1->expects(self::once())
            ->method('setState')
            ->with(ScheduleActivityState::CANCELLED_NO_GM);

        $session2 = $this->createMock(ScheduledActivity::class);
        $session2->expects(self::once())
            ->method('setState')
            ->with(ScheduleActivityState::CANCELLED_NO_GM);

        $repo = $this->createMock(ScheduledActivityRepository::class);
        $repo->expects(self::once())
            ->method('findStaleAwaitingGm')
            ->willReturn([$session1, $session2]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $tester = $this->buildTester($repo, $em);
        $tester->execute([]);

        self::assertSame(0, $tester->getStatusCode());
        self::assertStringContainsString('2', $tester->getDisplay());
    }

    public function testNoStaleSessions(): void
    {
        $repo = $this->createMock(ScheduledActivityRepository::class);
        $repo->expects(self::once())
            ->method('findStaleAwaitingGm')
            ->willReturn([]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $tester = $this->buildTester($repo, $em);
        $tester->execute([]);

        self::assertSame(0, $tester->getStatusCode());
    }

    private function buildTester(ScheduledActivityRepository $repo, EntityManagerInterface $em): CommandTester
    {
        $command = new SessionAutoCancelCommand($repo, $em);
        $application = new Application();
        $application->add($command);

        return new CommandTester($application->find('app:session:auto-cancel'));
    }
}
