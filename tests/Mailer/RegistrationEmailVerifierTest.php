<?php

namespace App\Tests\Mailer;

use App\Mailer\RegistrationEmailVerifier;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RegistrationEmailVerifierTest extends KernelTestCase
{
    public function testSendRegistrationConfirmation(): void
    {
        $service = self::bootKernel()->getContainer()->get(RegistrationEmailVerifier::class);

        self::markTestIncomplete('TODO');
    }

    public function testHandleEmailConfirmation(): void
    {
        $service = self::bootKernel()->getContainer()->get(RegistrationEmailVerifier::class);

        self::markTestIncomplete('TODO');
    }
}
