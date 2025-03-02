<?php

namespace App\Mailer;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

final readonly class PasswordResetMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private RouterInterface $router,
        private TranslatorInterface $translator,
        private string $emailSender,
    ) {
    }

    public function sendResettingEmailMessage(User $user, ResetPasswordToken $resetToken, string $locale): void
    {
        $url = $this->router->generate(
            'reset_password_from_token',
            ['token' => $resetToken->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $expiresAt = $this->translator->trans(
            $resetToken->getExpirationMessageKey(),
            $resetToken->getExpirationMessageData(),
            'ResetPasswordBundle',
        );

        $email = (new TemplatedEmail())
            ->from(new Address($this->emailSender, 'Conventionist'))
            ->to($user->getEmail())
            ->subject($this->translator->trans('password_reset.email.title'))
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'locale' => $locale,
                'signedUrl' => $url,
                'expiration' => $expiresAt,
            ])
        ;

        $this->mailer->send($email);
    }
}
