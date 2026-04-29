<?php

namespace App\Controller\Session;

use App\Entity\ScheduledActivity;
use App\Entity\User;
use App\Enum\ScheduleActivityState;
use App\Locales;
use App\Repository\ScheduledActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class GmCheckInController extends AbstractController
{
    public function __construct(
        private readonly ScheduledActivityRepository $scheduledActivities,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route(
        '/{_locale}/sessions/{id}/check-in',
        name: 'session_gm_check_in',
        requirements: ['_locale' => Locales::REGEX],
        methods: ['POST'],
    )]
    public function __invoke(string $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var ScheduledActivity|null $session */
        $session = $this->scheduledActivities->find($id);
        if (!$session) {
            throw $this->createNotFoundException();
        }

        $gm = $session->getSubmittedBy();
        if (!$gm || !$gm->isSameAs($user)) {
            throw $this->createAccessDeniedException();
        }

        $session->setGmCheckedInAt(new \DateTimeImmutable());
        if ($session->getState() === ScheduleActivityState::AWAITING_GM) {
            $session->setState(ScheduleActivityState::ACCEPTED);
        }

        $this->em->flush();

        $this->addFlash('success', 'session.gm_check_in.success');

        return $this->redirectToRoute('session_search', ['_locale' => $request->getLocale()]);
    }
}
