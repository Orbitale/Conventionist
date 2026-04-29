<?php

namespace App\EventSubscriber;

use App\Entity\EventRegistration;
use App\Enum\EventRegistrationStatus;
use App\Service\Waitlist\WaitlistPromoter;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postUpdate)]
final class EventRegistrationCancelledSubscriber
{
    public function __construct(
        private readonly WaitlistPromoter $waitlistPromoter,
    ) {
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof EventRegistration) {
            return;
        }

        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();
        $changeSet = $uow->getEntityChangeSet($entity);

        if (!isset($changeSet['status'])) {
            return;
        }

        [$oldStatus, $newStatus] = $changeSet['status'];

        if ($newStatus !== EventRegistrationStatus::CANCELLED) {
            return;
        }

        if ($oldStatus !== EventRegistrationStatus::CONFIRMED
            && $oldStatus !== EventRegistrationStatus::CHECKED_IN
            && $oldStatus !== EventRegistrationStatus::PENDING
        ) {
            return;
        }

        $this->waitlistPromoter->promoteForEvent($entity->getEvent());
    }
}
