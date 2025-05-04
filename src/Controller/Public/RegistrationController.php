<?php

namespace App\Controller\Public;

use App\Entity\User;
use App\Form\Type\RegistrationFormType;
use App\Mailer\RegistrationEmailVerifier;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class RegistrationController extends AbstractController
{
    public const string VERIFY_EMAIL_ROUTE_NAME = 'app_verify_email';

    public const array REGISTER_PATHS = [
        'fr' => '/inscription',
        'en' => '/register',
    ];

    public const array VERIFY_EMAIL_PATHS = [
        'fr' => '/verification-email',
        'en' => '/verify-email',
    ];

    public function __construct(
        private readonly RegistrationEmailVerifier $emailVerifier,
        private readonly TranslatorInterface $translator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route(self::REGISTER_PATHS, name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setLocale($request->getLocale());
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendRegistrationConfirmation($user, $request->attributes->get('_locale') ?: $request->getLocale());

            $this->addFlash('success', 'Registered!');

            return $this->redirectToRoute('index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route(self::VERIFY_EMAIL_PATHS, name: self::VERIFY_EMAIL_ROUTE_NAME, methods: ['GET', 'POST'])]
    public function verifyUserEmail(Request $request): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user || !$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->isMethod('GET')) {
                return $this->renderVerifiedLogin($request);
            }

            $csrf = $request->request->get('_csrf_token');
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $user = $this->userRepository->findOneBy(['username' => $username]);
            if (
                !$user
                || !$this->userPasswordHasher->isPasswordValid($user, $password)
                || !$this->csrfTokenManager->isTokenValid($this->csrfTokenManager->getToken($csrf))
            ) {
                $this->addFlash('error', 'Invalid credentials.');

                return $this->redirectToRoute('login');
            }
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', $this->translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('login');
    }

    private function renderVerifiedLogin(Request $request): Response
    {
        $request->getSession()->set('__email_verify_uri', $request->getRequestUri());

        $this->addFlash('info', 'Please login in to validate your email address.');

        return $this->render('login.html.twig', [
            'target_path' => $this->generateUrl(self::VERIFY_EMAIL_ROUTE_NAME),
            'remember_me_enabled' => false,
            'forgot_password_enabled' => false,
        ]);
    }
}
