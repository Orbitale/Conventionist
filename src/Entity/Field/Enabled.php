<?php

namespace App\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait Enabled
{
    #[ORM\Column(name: 'enabled', type: Types::BOOLEAN, nullable: false, options: ['default' => 0])]
    #[Assert\Type('bool')]
    #[Assert\NotNull]
    private bool $enabled = false;

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
