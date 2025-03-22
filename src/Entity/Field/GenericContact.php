<?php

namespace App\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait GenericContact
{
    #[ORM\Column(name: 'contact_name', type: Types::STRING, nullable: true)]
    private ?string $contactName = null;

    #[ORM\Column(name: 'contact_email', type: Types::STRING, nullable: true)]
    #[Assert\Email(mode: 'strict')]
    private ?string $contactEmail = null;

    #[ORM\Column(name: 'contact_phone', type: Types::STRING, nullable: true)]
    private ?string $contactPhone = null;

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    public function setContactName(?string $contactName): void
    {
        $this->contactName = $contactName;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): void
    {
        $this->contactEmail = $contactEmail;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): void
    {
        $this->contactPhone = $contactPhone;
    }
}
