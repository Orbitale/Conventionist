<?php

namespace App\Controller\Authenticated;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ActivitiesController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository
    )
    {}

    #[Route([
        'fr' => '/evenement/{slug}/activités/proposer',
        'en' => '/event/{slug}/activity/suggest',
    ], name: 'event_activities_register', methods: ['GET', 'POST'])]
    public function __invoke(string $slug): Response
    {
        $event = $this->eventRepository->findOneBy(['slug' => $slug]);
        if (!$event) {
            throw $this->createNotFoundException();
        }

        return $this->render('event/event.html.twig', [
            'event' => $event,
        ]);
    }
}
