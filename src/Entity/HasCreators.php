<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

interface HasCreators
{
    /**
     * @return Collection<User>
     */
    public function getCreators(): Collection;
}
