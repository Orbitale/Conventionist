<?php

namespace App\Twig\Extension;

use App\Entity\ScheduledActivity;
use App\Entity\User;
use App\Repository\AttendeeRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ActivityExtension extends AbstractExtension
{
    public function __construct(
        private readonly AttendeeRepository $attendeeRepository,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_user_registered_to_activity', $this->isUserRegisteredToActivity(...)),
        ];
    }

    public function isUserRegisteredToActivity(User $user, ScheduledActivity $activity): bool
    {
        $existingRegistration = $this->attendeeRepository->findOneBy([
            'registeredBy' => $user,
            'scheduledActivity' => $activity,
        ]);

        return $existingRegistration !== null;
    }
}
