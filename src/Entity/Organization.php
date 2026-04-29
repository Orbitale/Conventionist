<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ORM\Table(name: 'organization')]
class Organization implements HasCreators
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Creators { Field\Creators::__construct as generateCreators; }
    use Field\Description;
    use Field\Timestampable;
    use TimestampableEntity;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';

    #[ORM\Column(name: 'slug', type: Types::STRING, length: 255, unique: true, nullable: false)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = '';

    #[ORM\Column(name: 'website', type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Url(requireTld: true)]
    private ?string $website = null;

    #[ORM\Column(name: 'logo_path', type: Types::STRING, length: 255, nullable: true)]
    private ?string $logoPath = null;

    /** @var Collection<UserGroup> */
    #[ORM\OneToMany(targetEntity: UserGroup::class, mappedBy: 'organization')]
    private Collection $groups;

    public function __construct()
    {
        $this->generateId();
        $this->generateCreators();
        $this->generateTimestamps();
        $this->groups = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?: '';
    }

    public function getName(): string
    {
        return $this->name ?: '';
    }

    public function setName(?string $name): void
    {
        $this->name = $name ?: '';
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    public function setLogoPath(?string $logoPath): void
    {
        $this->logoPath = $logoPath;
    }

    /**
     * @return Collection<UserGroup>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }
}
