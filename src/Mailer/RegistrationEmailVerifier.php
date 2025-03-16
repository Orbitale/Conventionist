<?php

namespace App\Mailer;

use App\Controller\Public\RegistrationController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

final readonly class RegistrationEmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator,
        private string $emailSender,
    ) {
    }

    public function sendRegistrationConfirmation(User $user, string $locale): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            RegistrationController::VERIFY_EMAIL_ROUTE_NAME,
            $user->getId(),
            $user->getEmail()
        );

        $expiresAt = $this->translator->trans(
            $signatureComponents->getExpirationMessageKey(),
            $signatureComponents->getExpirationMessageData(),
            'ResetPasswordBundle',
        );

        $email = (new TemplatedEmail())
            ->from(new Address($this->emailSender, 'Conventionist'))
            ->to($user->getEmail())
            ->subject($this->translator->trans('registration.email.title'))
            ->htmlTemplate('registration/confirmation_email.html.twig')
            ->context([
                'locale' => $locale,
                'signedUrl' => $signatureComponents->getSignedUrl(),
                'expiration' => $expiresAt,
            ]);

        $this->mailer->send($email);
    }

    public function handleEmailConfirmation(Request $request, User $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, $user->getId(), $user->getEmail());

        $user->setEmailConfirmed();
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
