<?php

namespace App\Tests\TestUtils\Assertions;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

final class SessionHasFlashMessage extends Constraint
{
    public function __construct(
        private readonly string $messageType,
        private readonly string $message,
    ) {
    }

    public function toString(): string
    {
        return \sprintf('has flash message of type "%s" with value "%s"', $this->messageType, $this->message);
    }

    /**
     * @param Request $other
     */
    protected function matches($other): bool
    {
        if (!$other->hasSession()) {
            return false;
        }
        $session = $other->getSession();
        if (!$session instanceof FlashBagAwareSessionInterface) {
            return false;
        }
        $flashbag = $session->getFlashBag();

        return \in_array($this->message, $flashbag->peek($this->messageType), true);
    }

    /**
     * @param Request $other
     */
    protected function failureDescription($other): string
    {
        if (!$other->hasSession()) {
            return 'the Request does not have a Session';
        }
        $session = $other->getSession();
        if (!$session instanceof FlashBagAwareSessionInterface) {
            return 'the Session has no FlashBag';
        }

        return $this->toString();
    }
}
