<?php

namespace App\Controller\Public;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EventController extends AbstractController
{
    public const array PATHS = [
        'fr' => '/evenement/{slug}',
        'en' => '/event/{slug}',
    ];

    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {
    }

    #[Route(self::PATHS, name: 'event', methods: ['GET'])]
    public function __invoke(string $slug): Response
    {
        $event = $this->eventRepository->findOneWithRelations($slug);
        if (!$event) {
            throw $this->createNotFoundException();
        }

        return $this->render('event/event.html.twig', [
            'event' => $event,
        ]);
    }
}
