<?php

namespace App\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Slug
{
    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(name: 'slug', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $slug = null;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }
}
