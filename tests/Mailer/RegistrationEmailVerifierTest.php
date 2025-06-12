<?php

namespace App\Tests\Mailer;

use App\Controller\Public\RegistrationController;
use App\Entity\ScheduledActivity;
use App\Entity\User;
use App\Enum\ScheduleActivityState;
use App\Mailer\RegistrationEmailVerifier;
use App\Repository\ScheduledActivityRepository;
use App\Repository\UserRepository;
use App\Tests\TestUtils\GetUser;
use App\Tests\TestUtils\ProvidesLocales;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\LogicalAnd;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

final class RegistrationEmailVerifierTest extends KernelTestCase
{
    use ProvidesLocales;
    use GetUser;

    #[DataProvider('provideLocales')]
    public function testSendRegistrationConfirmation(string $locale): void
    {
        $user = new User();
        $user->setEmail('new_user@test.localhost');

        $service = new RegistrationEmailVerifier(
            $verifyHelperMock = $this->createMock(VerifyEmailHelperInterface::class),
            $mailerMock = $this->createMock(MailerInterface::class),
            new UserRepository($this->createMock(ManagerRegistry::class)),
            $this->createMock(EntityManagerInterface::class),
            $translatorMock = $this->createMock(TranslatorInterface::class),
            new ScheduledActivityRepository($this->createMock(ManagerRegistry::class)),
            $senderEmail = 'sender@test.localhost',
        );

        $verifyHelperMock
            ->expects($this->once())
            ->method('generateSignature')
            ->willReturn(new VerifyEmailSignatureComponents($date = new \DateTimeImmutable(), 'signed_uri', $date->getTimestamp()));

        $translatorMock->method('trans')->willReturnArgument(0);

        $actualEmail = null;

        $mailerMock
            ->expects($this->once())
            ->method('send')
            ->with(LogicalAnd::fromConstraints(
                new IsInstanceOf(TemplatedEmail::class),
                new Callback(function (TemplatedEmail $email) use (&$actualEmail) {
                    $actualEmail = $email;

                    return true;
                }),
            ))
        ;

        $service->sendRegistrationConfirmation($user, $locale);

        self::assertNotNull($actualEmail);
        self::assertSame('registration.email.title', $actualEmail->getSubject());
        self::assertSame(\sprintf('"Conventionist" <%s>', $senderEmail), $actualEmail->getFrom()[0]->toString());
        self::assertSame($user->getEmail(), $actualEmail->getTo()[0]->getAddress());
    }

    public function testHandleEmailConfirmationWithInexistendUser(): void
    {
        $container = self::bootKernel()->getContainer();
        $service = $container->get(RegistrationEmailVerifier::class);

        $user = new User();
        $user->setEmail('new_user@test.localhost');
        $request = new Request();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf('Could not find user with id %s', $user->getId()));

        $service->handleEmailConfirmation($request, $user);
    }

    public function testHandleEmailConfirmation(): void
    {
        $container = self::bootKernel()->getContainer();
        $service = $container->get(RegistrationEmailVerifier::class);
        /** @var VerifyEmailHelperInterface $verifyEmailHelper */
        $verifyEmailHelper = \Closure::bind(fn () => $this->verifyEmailHelper, $service, $service::class)->call($service, $service::class);
        /** @var ScheduledActivityRepository $scheduledActivityRepository */
        $scheduledActivityRepository = \Closure::bind(fn () => $this->scheduledActivityRepository, $service, $service::class)->call($service, $service::class);

        $user = $this->getUser('unvalidated');
        self::assertFalse($user->isEmailConfirmed());
        $activities = $scheduledActivityRepository->findBy(['submittedBy' => $user]);
        self::assertCount(2, $activities, 'Wrong number of activities found');
        self::assertSame(
            ['377f90c8-6ad3-4110-9c3a-595c8ea5e7a3', 'f3aeb57e-ea18-46f8-8221-8972c627cf49'],
            \array_map(static fn (ScheduledActivity $activity) => $activity->getId(), $activities),
        );
        self::assertSame(ScheduleActivityState::CREATED, $activities[0]?->getState());

        $signatureComponents = $verifyEmailHelper->generateSignature(RegistrationController::VERIFY_EMAIL_ROUTE_NAME, $user->getId(), $user->getEmail());

        $url = $signatureComponents->getSignedUrl();
        $parts = \parse_url($url);
        \parse_str($parts['query'], $query);
        $request = new Request($query, server: [
            'HTTP_HOST' => $parts['host'],
            'REQUEST_URI' => $parts['path'],
            'QUERY_STRING' => $parts['query'],
        ]);

        $service->handleEmailConfirmation($request, $user);

        self::assertTrue($user->isEmailConfirmed());
        $activities = $scheduledActivityRepository->findBy(['submittedBy' => $user]);
        self::assertCount(2, $activities);
        self::assertSame(
            ['377f90c8-6ad3-4110-9c3a-595c8ea5e7a3', 'f3aeb57e-ea18-46f8-8221-8972c627cf49'],
            \array_map(static fn (ScheduledActivity $activity) => $activity->getId(), $activities),
        );
        self::assertSame(ScheduleActivityState::PENDING_REVIEW, $activities[0]?->getState());
    }
}
