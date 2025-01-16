<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;

readonly class LocaleListener implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private LocaleAwareInterface  $translator,
    ) {
    }

    public function onRequestBeforeLocaleListener(RequestEvent $event): void
    {
        $req = $event->getRequest();

        if ($req->attributes->has('_locale')) {
            // Some other part of the app must have overriden the locale: let it go
            return;
        }

        if ($req->query->has('lang')) {
            $req->attributes->set('_locale', $req->query->get('lang'));
        }
    }

    public function onRequestAfterFirewall(RequestEvent $event): void
    {
        $req = $event->getRequest();

        if ($req->attributes->has('_locale')) {
            // Some other part of the app must have overriden the locale: let it go
            return;
        }

        $user = $this->tokenStorage->getToken()?->getUser();
        if ($user instanceof User) {
            $req->attributes->set('_locale', $user->getLocale());
            $this->translator->setLocale($user->getLocale());
        }
    }

    /**
     * @see \Symfony\Component\HttpKernel\EventListener\LocaleListener::getSubscribedEvents
     * @see \Symfony\Bundle\SecurityBundle\EventListener\FirewallListener::getSubscribedEvents
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onRequestBeforeLocaleListener', 17],
                ['onRequestAfterFirewall', 7],
            ],
        ];
    }
}
