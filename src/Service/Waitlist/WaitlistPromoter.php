<?php

namespace App\Service\Waitlist;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Enum\EventRegistrationStatus;
use App\Repository\EventRegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class WaitlistPromoter
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventRegistrationRepository $registrationRepository,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * @return array<EventRegistration> Registrations that were promoted from WAITLIST to CONFIRMED.
     */
    public function promoteForEvent(Event $event): array
    {
        if (!$event->isWaitlistEnabled()) {
            return [];
        }

        $capacity = $event->getCapacity();
        if (null === $capacity) {
            return [];
        }

        // Count seats currently occupying capacity.
        $occupyingStatuses = [
            EventRegistrationStatus::CONFIRMED,
            EventRegistrationStatus::CHECKED_IN,
            EventRegistrationStatus::PENDING,
        ];

        $occupied = \count($this->registrationRepository->findBy([
            'event' => $event,
            'status' => $occupyingStatuses,
        ]));

        $available = $capacity - $occupied;
        if ($available <= 0) {
            return [];
        }

        $waitlisted = $this->registrationRepository->findBy(
            ['event' => $event, 'status' => EventRegistrationStatus::WAITLIST],
            ['registeredAt' => 'ASC'],
            $available,
        );

        if (!$waitlisted) {
            return [];
        }

        $promoted = [];
        $now = new \DateTimeImmutable();

        foreach ($waitlisted as $registration) {
            $registration->setStatus(EventRegistrationStatus::CONFIRMED);
            if (null === $registration->getConfirmedAt()) {
                $registration->setConfirmedAt($now);
            }
            $promoted[] = $registration;

            $this->logger?->info('Promoted waitlisted registration to confirmed.', [
                'registration_id' => (string) $registration->getId(),
                'event_id' => (string) $event->getId(),
            ]);
        }

        $this->entityManager->flush();

        return $promoted;
    }
}
