<?php

namespace App\Controller\Public;

use App\Enum\ScheduleActivityState;
use App\Locales;
use App\Repository\ScheduledActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SessionSearchController extends AbstractController
{
    private const int PER_PAGE = 20;

    public function __construct(
        private readonly ScheduledActivityRepository $scheduledActivityRepository,
    ) {
    }

    #[Route(
        '/{_locale}/sessions',
        name: 'session_search',
        requirements: ['_locale' => Locales::REGEX],
        methods: ['GET'],
    )]
    public function __invoke(Request $request): Response
    {
        $rawFilters = [
            'event' => $request->query->get('event'),
            'game' => $request->query->get('game'),
            'category' => $request->query->get('category'),
            'theme' => $request->query->get('theme'),
            'state' => $request->query->get('state'),
            'from' => $request->query->get('from'),
            'to' => $request->query->get('to'),
            'seats' => $request->query->get('seats'),
            'q' => $request->query->get('q'),
        ];

        $filters = [
            'event' => $this->nonEmpty($rawFilters['event']),
            'game' => $this->nonEmpty($rawFilters['game']),
            'category' => $this->nonEmpty($rawFilters['category']),
            'theme' => $this->nonEmpty($rawFilters['theme']),
            'state' => $this->parseState($rawFilters['state']),
            'from' => $this->parseDate($rawFilters['from']),
            'to' => $this->parseDate($rawFilters['to']),
            'seats' => \in_array($rawFilters['seats'], ['any', 'available'], true) ? $rawFilters['seats'] : null,
            'q' => $this->nonEmpty($rawFilters['q']),
        ];

        $page = max(1, (int) $request->query->get('page', 1));

        $result = $this->scheduledActivityRepository->searchPaginated($filters, $page, self::PER_PAGE);

        $totalPages = (int) max(1, (int) ceil($result['total'] / self::PER_PAGE));

        return $this->render('session_search/index.html.twig', [
            'items' => $result['items'],
            'total' => $result['total'],
            'page' => $page,
            'per_page' => self::PER_PAGE,
            'total_pages' => $totalPages,
            'filters' => $rawFilters,
            'states' => ScheduleActivityState::cases(),
        ]);
    }

    private function nonEmpty(mixed $value): ?string
    {
        if (!\is_string($value)) {
            return null;
        }
        $value = trim($value);

        return '' === $value ? null : $value;
    }

    private function parseState(mixed $value): ?ScheduleActivityState
    {
        if (!\is_string($value) || '' === $value) {
            return null;
        }

        return ScheduleActivityState::tryFrom($value);
    }

    private function parseDate(mixed $value): ?\DateTimeImmutable
    {
        if (!\is_string($value) || '' === trim($value)) {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception) {
            // Invalid date input is ignored silently — the filter is simply not applied.
            return null;
        }
    }
}
