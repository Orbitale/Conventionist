<?php

namespace App\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

trait StartEndDates
{
    #[ORM\Column(name: 'starts_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Assert\LessThan(propertyPath: 'endsAt')]
    private \DateTimeImmutable $startsAt;

    #[ORM\Column(name: 'ends_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Assert\GreaterThan(propertyPath: 'startsAt')]
    private \DateTimeImmutable $endsAt;

    public function getStartsAt(): \DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeImmutable $startsAt): void
    {
        $this->startsAt = $startsAt;
    }

    public function getEndsAt(): \DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(\DateTimeImmutable $endsAt): void
    {
        $this->endsAt = $endsAt;
    }

    /**
     * Duration is rounded to the upper hour.
     */
    public function getDurationInHours(): int
    {
        return \ceil(\abs($this->endsAt->getTimestamp() - $this->startsAt->getTimestamp()) / 60);
    }
}
