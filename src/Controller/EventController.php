<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository
    )
    {}

    #[Route([
        'fr' => '/evenement/{slug}',
        'en' => '/event/{slug}',
    ], name: 'event', methods: ['GET'])]
    public function __invoke(string $slug): Response
    {
        $event = $this->eventRepository->findOneBy(['slug' => $slug]);
        if (!$event) {
            throw new NotFoundHttpException();
        }

        return $this->render('event/event.html.twig', [
            'event' => $event,
        ]);
    }
}
