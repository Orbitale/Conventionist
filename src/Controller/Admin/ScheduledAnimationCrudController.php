<?php

namespace App\Controller\Admin;

use App\Entity\ScheduledAnimation;
use App\Repository\ScheduledAnimationRepository;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ScheduledAnimationCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ScheduledAnimationRepository $scheduledAnimationRepository,
        private readonly TranslatorInterface $translator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly RequestStack $requestStack,
        private readonly TimeSlotRepository $timeSlotRepository,
    ) {
    }

    #[Route("/admin/scheduled-animation/accept/{id}", name: "admin_scheduled_animation_accept", methods: ['POST'])]
    public function acceptSchedule(Request $request, string $id): Response
    {
        $scheduledAnimation = $this->scheduledAnimationRepository->find($id);
        if (!$scheduledAnimation) {
            throw new NotFoundHttpException('Scheduled animation not found with this ID.');
        }

        $csrfToken = $request->request->get('_csrf');
        if (!$csrfToken || !$this->csrfTokenManager->isTokenValid($this->csrfTokenManager->getToken($csrfToken))) {
            throw new BadRequestHttpException('Invalid CSRF token.');
        }

        /** @var array<ScheduledAnimation> $animationsAtSameTimeSlot */
        $animationsAtSameTimeSlot = $this->scheduledAnimationRepository->findAtSameTimeSlot($scheduledAnimation);

        foreach ($animationsAtSameTimeSlot as $otherSchedule) {
            if ($otherSchedule->isAccepted()) {
                $this->addFlash('error', 'Cannot accept schedule: it conflicts with another schedule at the same time and table.');

                return $this->redirectToRoute('admin_calendar_event', ['event_id' => $scheduledAnimation->getEvent()->getId()]);
            }
            $otherSchedule->reject();
            $this->em->persist($otherSchedule);
        }
        $scheduledAnimation->accept();

        $this->em->persist($scheduledAnimation);
        $this->em->flush();

        return $this->redirectToRoute('admin_calendar_event', ['event_id' => $scheduledAnimation->getEvent()->getId()]);
    }

    #[Route("/admin/scheduled-animation/reject/{id}", name: "admin_scheduled_animation_reject", methods: ['POST'])]
    public function rejectSchedule(Request $request, string $id): Response
    {
        $scheduledAnimation = $this->scheduledAnimationRepository->find($id);
        if (!$scheduledAnimation) {
            throw new NotFoundHttpException('Scheduled animation not found with this ID.');
        }

        $csrfToken = $request->request->get('_csrf');
        if (!$csrfToken || !$this->csrfTokenManager->isTokenValid($this->csrfTokenManager->getToken($csrfToken))) {
            throw new BadRequestHttpException('Invalid CSRF token.');
        }

        if ($scheduledAnimation->isAccepted()) {
            $this->addFlash('error', 'Cannot reject schedule: it was already accepted earlier.');

            return $this->redirectToRoute('admin_calendar_event', ['event_id' => $scheduledAnimation->getEvent()->getId()]);
        }
        $scheduledAnimation->reject();

        $this->em->persist($scheduledAnimation);
        $this->em->flush();

        return $this->redirectToRoute('admin_calendar_event', ['event_id' => $scheduledAnimation->getEvent()->getId()]);
    }

    public static function getEntityFqcn(): string
    {
        return ScheduledAnimation::class;
    }

    public function createEntity(string $entityFqcn): ScheduledAnimation
    {
        /** @var ScheduledAnimation $scheduledAnimation */
        $scheduledAnimation = parent::createEntity($entityFqcn);

        $request = $this->requestStack->getCurrentRequest();
        if ($request && $request->query->has('slot_id')) {
            $slot = $this->timeSlotRepository->find($request->query->get('slot_id'));
            if ($slot) {
                $scheduledAnimation->setTimeSlot($slot);
            }
        }

        return $scheduledAnimation;
    }

    public function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        $this->addFlashAfterSave($context, $action);

        /** @var ScheduledAnimation $scheduledAnimation */
        $scheduledAnimation = $context->getEntity()->getInstance();
        $event = $scheduledAnimation->getEvent()->getId();

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

        $qb->innerJoin('entity.animation', 'animation')
            ->innerJoin('animation.creators', 'creators')
            ->andWhere('creators IN (:creator)')
            ->setParameter('creator', $this->getUser())
        ;

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        $request = $this->requestStack->getCurrentRequest();

        yield Field\TextField::new('id')->hideOnForm();
        yield Field\TextField::new('stateString')->setDisabled()->hideWhenCreating();
        yield Field\AssociationField::new('animation')->setRequired(true);
        yield Field\AssociationField::new('timeSlot')->setRequired(true)->setDisabled($request?->query->has('slot_id') ?: false);
    }
}
