<?php

namespace App\Controller\Public;

use App\Entity\Notification;
use App\Entity\User;
use App\Locales;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class NotificationController extends AbstractController
{
    public function __construct(
        private readonly NotificationRepository $notifications,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route(
        '/{_locale}/notifications',
        name: 'notifications_index',
        requirements: ['_locale' => Locales::REGEX],
        methods: ['GET'],
    )]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('notification/index.html.twig', [
            'notifications' => $this->notifications->findForRecipient($user),
            'unread_count' => $this->notifications->countUnreadForRecipient($user),
        ]);
    }

    #[Route(
        '/{_locale}/notifications/read-all',
        name: 'notifications_read_all',
        requirements: ['_locale' => Locales::REGEX],
        methods: ['POST'],
    )]
    public function readAll(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->notifications->markAllAsReadFor($user);
        $this->addFlash('success', 'All notifications marked as read.');

        return $this->redirectToRoute('notifications_index');
    }

    #[Route(
        '/{_locale}/notifications/{id}/read',
        name: 'notifications_read',
        requirements: ['_locale' => Locales::REGEX],
        methods: ['POST'],
    )]
    public function read(string $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Notification|null $notification */
        $notification = $this->notifications->find($id);
        if (!$notification || !$notification->getRecipient()->isSameAs($user)) {
            throw $this->createNotFoundException();
        }

        $notification->markAsRead();
        $this->em->flush();

        return $this->redirectToRoute('notifications_index');
    }
}
