<?php

namespace App\Controller\Public;

use App\Entity\Attendee;
use App\Entity\ScheduledActivity;
use App\Entity\User;
use App\Form\RegisterAsActivityAttendeeType;
use App\Repository\AttendeeRepository;
use App\Repository\ScheduledActivityRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ActivityAttendeeRegisterController extends AbstractController
{
    public const array PATHS = [
        'fr' => '/inscription/activite/{id}',
        'en' => '/register/activity/{id}',
    ];

    public function __construct(
        private readonly ScheduledActivityRepository $scheduledActivityRepository,
        private readonly AttendeeRepository $attendeeRepository,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route(self::PATHS, name: 'register_to_activity', methods: ['GET', 'POST'])]
    public function __invoke(string $id, Request $request): Response
    {
        /** @var ScheduledActivity|null $activity */
        $activity = $this->scheduledActivityRepository->find($id);

        if (!$activity) {
            throw $this->createNotFoundException();
        }
        if (!$activity->canBeRegisteredTo()) {
            $this->addFlash('danger', 'event.error.cannot_register_to_activity');

            return $this->redirectToRoute('event', ['slug' => $activity->getEvent()->getSlug()]);
        }

        /** @var User|null $user */
        $user = $this->getUser();
        if ($user) {
            $existingRegistration = $this->attendeeRepository->findOneBy([
                'registeredBy' => $user,
                'scheduledActivity' => $activity,
            ]);
            if ($existingRegistration) {
                $this->addFlash('danger', 'event.error.already_registered_to_activity');

                return $this->redirectToRoute('event', ['slug' => $activity->getEvent()->getSlug()]);
            }
        }

        $attendee = new Attendee();
        $attendee->setScheduledActivity($activity);

        if ($user) {
            $attendee->setRegisteredBy($user);
        }
        $form = $this->createForm(RegisterAsActivityAttendeeType::class, $attendee, [
            'enable_email' => $user === null,
            'login_url' => $this->generateUrl('login', ['target' => $request->getPathInfo()]),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user) {
                $user = $this->userRepository->findOneBy(['email' => $attendee->email]);
            }

            if (!$user) {
                $user = new User();
                $user->setUsername(\preg_replace('~@.*$~sUu', '', $attendee->email));
                $user->setPassword(\bin2hex(\random_bytes(48)));
                $user->setEmail($attendee->email);
            }

            $attendee->email = null;
            $attendee->setRegisteredBy($user);

            $this->em->persist($attendee);
            $this->em->flush();

            $this->addFlash('success', 'event.activity.register_as_attendee.success');

            return $this->redirectToRoute('event', ['slug' => $activity->getEvent()->getSlug()]);
        }

        return $this->render('event/register_to_activity.html.twig', [
            'scheduled_activity' => $activity,
            'form' => $form->createView(),
        ]);
    }
}
