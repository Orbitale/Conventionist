<?php

namespace App\Controller\Public;

use App\Entity\ScheduledActivity;
use App\Entity\TimeSlot;
use App\Entity\User;
use App\Enum\ScheduleActivityState;
use App\Form\Type\SubmitActivityToSlotType;
use App\Repository\TimeSlotRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SubmitActivityToSlotController extends AbstractController
{
    public const array PATHS = [
        'fr' => '/inscription/creneau/{id}',
        'en' => '/register/slot/{id}',
    ];

    public function __construct(
        private readonly TimeSlotRepository $timeSlotRepository,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route(self::PATHS, name: 'submit_activity_to_slot', methods: ['GET', 'POST'])]
    public function __invoke(string $id, Request $request): Response
    {
        /** @var TimeSlot|null $slot */
        $slot = $this->timeSlotRepository->find($id);

        if (!$slot) {
            throw $this->createNotFoundException();
        }
        if ($slot->isClosedForPlanning()) {
            $this->addFlash('danger', 'event.error.cannot_submit_activity_to_slot');

            return $this->redirectToRoute('event', ['slug' => $slot->getEvent()->getSlug()]);
        }

        /** @var User|null $user */
        $user = $this->getUser();

        $scheduledActivity = new ScheduledActivity();
        $scheduledActivity->setState(ScheduleActivityState::CREATED);
        $scheduledActivity->setTimeSlot($slot);
        if ($user) {
            $scheduledActivity->setSubmittedBy($user);
        }

        $form = $this->createForm(SubmitActivityToSlotType::class, $scheduledActivity, [
            'user' => $user,
            'time_slot' => $slot,
            'login_url' => $this->generateUrl('login', ['target' => $request->getPathInfo()]),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user) {
                $user = $this->userRepository->findOneBy(['email' => $scheduledActivity->email]);
            }

            if (!$user) {
                $user = new User();
                $user->setUsername(\preg_replace('~@.*$~sUu', '', $scheduledActivity->email));
                $user->setPassword(\bin2hex(\random_bytes(48)));
                $user->setEmail($scheduledActivity->email);
                $user->setLocale($request->getLocale());
            }

            if ($scheduledActivity->newActivity) {
                $scheduledActivity->newActivity->addCreator($user);
                $this->em->persist($scheduledActivity->newActivity);
            }

            $scheduledActivity->newActivity = null;
            $scheduledActivity->selectedActivity = null;
            $scheduledActivity->setSubmittedBy($user);

            $this->em->persist($scheduledActivity);
            $this->em->flush();

            $this->addFlash('success', 'event.activity.submit_activity.success');

            return $this->redirectToRoute('event', ['slug' => $slot->getEvent()->getSlug()]);
        }

        return $this->render('event/submit_activity_to_slot.html.twig', [
            'time_slot' => $slot,
            'form' => $form->createView(),
        ]);
    }
}
