<?php

namespace App\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait Published
{
    #[ORM\Column(name: 'is_published', type: Types::BOOLEAN, nullable: false, options: ['default' => 0])]
    #[Assert\Type('bool')]
    #[Assert\NotNull]
    private bool $published = false;

    public function getPublished(): bool
    {
        return $this->published;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }
}
