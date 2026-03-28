<?php

namespace App\Controller\Admin;

use App\Repository\EventRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class CloneEventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {
    }

    #[AdminRoute('/event-clone', name: 'admin_event_clone', options: ['methods' => ['GET', 'POST']])]
    public function __invoke(): Response
    {
        $user = $this->isGranted('ROLE_ADMIN') ? null : $this->getUser();
        $events = $this->eventRepository->findForUser($user);

        return $this->render('admin/event/clone.html.twig', [
            'events' => $events,
        ]);
    }
}
