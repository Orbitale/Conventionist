<?php

namespace App\Command;

use App\Enum\ScheduleActivityState;
use App\Repository\ScheduledActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:session:auto-cancel',
    description: 'Cancel ScheduledActivity sessions whose GM did not check in before the grace deadline.',
)]
final class SessionAutoCancelCommand extends Command
{
    public function __construct(
        private readonly ScheduledActivityRepository $scheduledActivities,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'List affected sessions without persisting changes.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');

        $now = new \DateTimeImmutable();
        $stale = $this->scheduledActivities->findStaleAwaitingGm($now);
        $count = \count($stale);

        if ($count === 0) {
            $io->success('No stale AWAITING_GM sessions to cancel.');

            return Command::SUCCESS;
        }

        foreach ($stale as $session) {
            if (!$dryRun) {
                $session->setState(ScheduleActivityState::CANCELLED_NO_GM);
            }
        }

        if ($dryRun) {
            $io->note(\sprintf('[dry-run] %d session(s) would be cancelled.', $count));
        } else {
            $this->em->flush();
            $io->success(\sprintf('Cancelled %d session(s) due to missing GM check-in.', $count));
        }

        return Command::SUCCESS;
    }
}
