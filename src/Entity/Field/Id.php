<?php

namespace App\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait Id
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::STRING, length: '36')]
    private string $id;

    public function __construct()
    {
        $this->id = Uuid::v7()->toString();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isSameAs(?object $object): bool
    {
        $className = \get_class($this);

        if (!$object || !$object instanceof $className) {
            return false;
        }

        return $object->getId() === $this->id;
    }
}
