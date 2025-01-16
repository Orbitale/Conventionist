<?php

namespace App\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait Description
{
    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: false)]
    private string $description = '';

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description ?: '';
    }
}
