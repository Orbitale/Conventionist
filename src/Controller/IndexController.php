<?php

namespace App\Controller;

use App\Entity\User;
use App\Locales;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    public function __construct(private readonly EventRepository $eventRepository)
    {
    }

    #[Route('/', name: 'root', methods: ['GET'])]
    public function root(Request $request): Response
    {
        $locale = $request->attributes->has('_locale');
        if (!$locale && $user = $this->getUser()) {
            /** @var User $user */
            $locale = $user->getLocale();
        } else {
            $locale = $request->getPreferredLanguage(Locales::LOCALES);
        }

        return $this->redirectToRoute('index', ['_locale' => $locale]);
    }

    #[Route('/{_locale}', name: 'index', requirements: ['_locale' => Locales::REGEX], methods: ['GET'])]
    public function index(): Response
    {
        $events = $this->eventRepository->findUpcoming(null);

        return $this->render('index/index.html.twig', [
            'events' => $events,
        ]);
    }
}
