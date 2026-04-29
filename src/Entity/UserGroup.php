<?php

namespace App\Entity;

use App\Repository\UserGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserGroupRepository::class)]
#[ORM\Table(name: 'user_group')]
class UserGroup implements HasCreators
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

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private Organization $organization;

    /** @var Collection<UserGroupMembership> */
    #[ORM\OneToMany(targetEntity: UserGroupMembership::class, mappedBy: 'userGroup', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $memberships;

    public function __construct()
    {
        $this->generateId();
        $this->generateCreators();
        $this->generateTimestamps();
        $this->memberships = new ArrayCollection();
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

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * @return Collection<UserGroupMembership>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(UserGroupMembership $membership): void
    {
        if ($this->memberships->contains($membership)) {
            return;
        }

        $this->memberships->add($membership);
        $membership->setUserGroup($this);
    }

    public function removeMembership(UserGroupMembership $membership): void
    {
        $this->memberships->removeElement($membership);
    }
}
