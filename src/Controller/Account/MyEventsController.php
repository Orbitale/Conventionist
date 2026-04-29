<?php

namespace App\Controller\Account;

use App\Entity\EventRegistration;
use App\Entity\ScheduledActivity;
use App\Entity\User;
use App\Enum\EventRegistrationStatus;
use App\Locales;
use App\Repository\EventRegistrationRepository;
use App\Repository\ScheduledActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class MyEventsController extends AbstractController
{
    public function __construct(
        private readonly EventRegistrationRepository $registrations,
        private readonly ScheduledActivityRepository $scheduledActivities,
    ) {
    }

    #[Route(
        '/{_locale}/my/events',
        name: 'account_my_events',
        requirements: ['_locale' => Locales::REGEX],
        methods: ['GET'],
    )]
    public function myEvents(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $registrations = $this->registrations->findForUser($user);

        $now = new \DateTimeImmutable();
        $groups = [
            'confirmed' => [],
            'pending' => [],
            'waitlist' => [],
            'past' => [],
            'cancelled' => [],
        ];

        foreach ($registrations as $registration) {
            /** @var EventRegistration $registration */
            $event = $registration->getEvent();
            if ($event->getEndsAt() !== null && $event->getEndsAt() < $now) {
                $groups['past'][] = $registration;
                continue;
            }

            $groups[match ($registration->getStatus()) {
                EventRegistrationStatus::CONFIRMED, EventRegistrationStatus::CHECKED_IN => 'confirmed',
                EventRegistrationStatus::PENDING => 'pending',
                EventRegistrationStatus::WAITLIST => 'waitlist',
                EventRegistrationStatus::CANCELLED => 'cancelled',
            }][] = $registration;
        }

        return $this->render('account/my_events.html.twig', [
            'groups' => $groups,
        ]);
    }

    #[Route(
        '/{_locale}/my/agenda',
        name: 'account_my_agenda',
        requirements: ['_locale' => Locales::REGEX],
        methods: ['GET'],
    )]
    public function myAgenda(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $today = new \DateTimeImmutable('today');
        $from = $this->parseDate($request->query->get('from')) ?? $today;
        $to = $this->parseDate($request->query->get('to')) ?? $from->modify('+30 days');
        if ($to < $from) {
            $to = $from->modify('+30 days');
        }
        // Normalize range ends (inclusive day).
        $from = $from->setTime(0, 0, 0);
        $to = $to->setTime(23, 59, 59);

        $sessions = $this->scheduledActivities->findUpcomingForUser($user, $from, $to);

        $byDay = [];
        foreach ($sessions as $session) {
            /** @var ScheduledActivity $session */
            $key = $session->getStartsAt()->format('Y-m-d');
            $byDay[$key][] = $session;
        }
        ksort($byDay);

        return $this->render('account/agenda.html.twig', [
            'days' => $byDay,
            'from' => $from,
            'to' => $to,
        ]);
    }

    private function parseDate(?string $raw): ?\DateTimeImmutable
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($raw);
        } catch (\Exception) {
            return null;
        }
    }
}
