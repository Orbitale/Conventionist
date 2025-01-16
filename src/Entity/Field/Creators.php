<?php

namespace App\Entity\Field;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait Creators
{
    /** @var Collection<User> */
    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $creators;

    public function __construct()
    {
        $this->creators = new ArrayCollection();
    }

    /**
     * @return Collection<User>
     */
    public function getCreators(): Collection
    {
        return $this->creators;
    }

    public function addCreator(User $user): void
    {
        if ($this->creators->contains($user)) {
            return;
        }

        $this->creators->add($user);
    }

    public function hasCreator(User $user): bool
    {
        return $this->creators->contains($user);
    }
}
