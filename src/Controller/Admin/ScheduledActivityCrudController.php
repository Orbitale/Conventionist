<?php

namespace App\Controller\Admin;

use App\Entity\ScheduledActivity;
use App\Repository\ScheduledActivityRepository;
use App\Repository\TimeSlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ScheduledActivityCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ScheduledActivityRepository $scheduledActivityRepository,
        private readonly TranslatorInterface $translator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly RequestStack $requestStack,
        private readonly TimeSlotRepository $timeSlotRepository,
    ) {
    }

    #[Route('/admin/scheduled-activity/accept/{id}', name: 'admin_scheduled_activity_accept', methods: ['POST'])]
    public function acceptSchedule(Request $request, string $id): Response
    {
        $scheduledActivity = $this->scheduledActivityRepository->find($id);
        if (!$scheduledActivity) {
            throw new NotFoundHttpException('Scheduled activity not found with this ID.');
        }

        $csrfToken = $request->request->get('_csrf');
        if (!$csrfToken || !$this->csrfTokenManager->isTokenValid($this->csrfTokenManager->getToken($csrfToken))) {
            throw new BadRequestHttpException('Invalid CSRF token.');
        }

        /** @var array<ScheduledActivity> $activitiesAtSameTimeSlot */
        $activitiesAtSameTimeSlot = $this->scheduledActivityRepository->findAtSameTimeSlot($scheduledActivity);

        foreach ($activitiesAtSameTimeSlot as $otherSchedule) {
            if ($otherSchedule->isAccepted()) {
                $this->addFlash('error', 'Cannot accept schedule: it conflicts with another schedule at the same time and booth.');

                return $this->redirectToRoute('admin_calendar_event', ['event_id' => $scheduledActivity->getEvent()->getId()]);
            }
            $otherSchedule->reject();
            $this->em->persist($otherSchedule);
        }
        $scheduledActivity->accept();

        $this->em->persist($scheduledActivity);
        $this->em->flush();

        return $this->redirectToRoute('admin_calendar_event', ['event_id' => $scheduledActivity->getEvent()->getId()]);
    }

    #[Route('/admin/scheduled-activity/reject/{id}', name: 'admin_scheduled_activity_reject', methods: ['POST'])]
    public function rejectSchedule(Request $request, string $id): Response
    {
        $scheduledActivity = $this->scheduledActivityRepository->find($id);
        if (!$scheduledActivity) {
            throw new NotFoundHttpException('Scheduled activity not found with this ID.');
        }

        $csrfToken = $request->request->get('_csrf');
        if (!$csrfToken || !$this->csrfTokenManager->isTokenValid($this->csrfTokenManager->getToken($csrfToken))) {
            throw new BadRequestHttpException('Invalid CSRF token.');
        }

        if ($scheduledActivity->isAccepted()) {
            $this->addFlash('error', 'Cannot reject schedule: it was already accepted earlier.');

            return $this->redirectToRoute('admin_calendar_event', ['event_id' => $scheduledActivity->getEvent()->getId()]);
        }
        $scheduledActivity->reject();

        $this->em->persist($scheduledActivity);
        $this->em->flush();

        return $this->redirectToRoute('admin_calendar_event', ['event_id' => $scheduledActivity->getEvent()->getId()]);
    }

    public static function getEntityFqcn(): string
    {
        return ScheduledActivity::class;
    }

    public function createEntity(string $entityFqcn): ScheduledActivity
    {
        /** @var ScheduledActivity $scheduledActivity */
        $scheduledActivity = parent::createEntity($entityFqcn);

        $request = $this->requestStack->getCurrentRequest();
        if ($request && $request->query->has('slot_id')) {
            $slot = $this->timeSlotRepository->find($request->query->get('slot_id'));
            if ($slot) {
                $scheduledActivity->setTimeSlot($slot);
            }
        }

        return $scheduledActivity;
    }

    public function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        $this->addFlashAfterSave($context, $action);

        /** @var ScheduledActivity $scheduledActivity */
        $scheduledActivity = $context->getEntity()->getInstance();
        $event = $scheduledActivity->getEvent()->getId();

        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

        if ($request->query->has('slot_id')) {
            return $this->redirectToRoute('admin_calendar_event', ['event_id' => $event]);
        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::EDIT, Action::DELETE);

        return $actions;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        $qb->innerJoin('entity.activity', 'activity')
            ->innerJoin('activity.creators', 'creators')
            ->andWhere('creators IN (:creator)')
            ->setParameter('creator', $this->getUser())
        ;

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        $request = $this->requestStack->getCurrentRequest();

        yield Field\ChoiceField::new('state')->setDisabled()->hideWhenCreating();
        yield Field\AssociationField::new('activity')->setRequired(true);
        yield Field\AssociationField::new('timeSlot')->setRequired(true)->setDisabled($request?->query->has('slot_id') ?: false);
    }
}
