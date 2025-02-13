<?php

namespace App\Controller\Admin;

use App\Entity\TimeSlot;
use App\Repository\EventRepository;
use App\Repository\BoothRepository;
use App\Repository\TimeSlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TimeSlotCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventRepository $eventRepository,
        private readonly BoothRepository $boothRepository,
        private readonly TimeSlotRepository $scheduledAnimationRepository,
        private readonly TranslatorInterface $translator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    #[Route("/admin/timeslot/create-from-calendar", name: "admin_timeslot_create_from_calendar", methods: ['POST'])]
    public function createFromCalendar(Request $request): Response
    {
        $csrfToken = $request->request->get('_csrf');
        if (!$csrfToken || !$this->csrfTokenManager->isTokenValid($this->csrfTokenManager->getToken($csrfToken))) {
            throw new BadRequestHttpException('Invalid CSRF token.');
        }

        $start = $request->request->get('start');
        $end = $request->request->get('end');
        $booth = $request->request->get('booth_id');
        $event = $request->request->get('event_id');

        if (!$start || !$end || !$booth || !$event) {
            throw new BadRequestHttpException('Missing required parameters.');
        }
        try {
            $start = new \DateTimeImmutable($start)->setTimezone(new \DateTimeZone('UTC'));
            $end = new \DateTimeImmutable($end)->setTimezone(new \DateTimeZone('UTC'));
        } catch (\DateMalformedStringException) {
            $start = null;
            $end = null;
        }
        $booth = $this->boothRepository->find($booth);
        $event = $this->eventRepository->find($event);
        if (!$start || !$end || !$booth || !$event) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $newSlot = TimeSlot::create($event, $table, $start, $end);
        $this->em->persist($newSlot);
        $this->em->flush();

        $this->addFlash('success', 'Successfully created new time slot!');

        $url = $request->headers->get('referer');
        if (!$url) {
            $url = $this->generateUrl('admin_calendar_event', ['event_id' => $event->getId()]);
        }

        return $this->redirect($url);
    }

    public static function getEntityFqcn(): string
    {
        return TimeSlot::class;
    }

    //

    public function configureFields(string $pageName): iterable
    {
        yield Field\AssociationField::new('event')->setRequired(true);
        yield Field\AssociationField::new('booth')->setRequired(true);
        yield Field\DateTimeField::new('startsAt')->setTimezone('UTC');
        yield Field\DateTimeField::new('endsAt')->setTimezone('UTC');
    }
}
