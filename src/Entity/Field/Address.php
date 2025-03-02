<?php

namespace App\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait Address
{
    #[ORM\Column(name: "address1", type: Types::STRING, nullable: true)]
    private ?string $address1 = null;

    #[ORM\Column(name: "address2", type: Types::STRING, nullable: true)]
    private ?string $address2 = null;

    #[ORM\Column(name: "state", type: Types::STRING, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(name: "zip_code", type: Types::STRING, nullable: true)]
    private ?string $zipCode = null;

    #[ORM\Column(name: "city", type: Types::STRING, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(name: "country", type: Types::STRING, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(name: "latitude", type: Types::STRING, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(name: "longitude", type: Types::STRING, nullable: true)]
    private ?string $longitude = null;

    public function getAddress(): string
    {
        return $this->address1
            . $this->address2
            . $this->zipCode
            . $this->city
            . $this->state
            . $this->country;
    }

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(?string $address1): void
    {
        $this->address1 = $address1;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): void
    {
        $this->address2 = $address2;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): void
    {
        $this->longitude = $longitude;
    }
}
