<?php

namespace App\Entity\Behaviors;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\VarDumper\Cloner\VarCloner;

trait CloneVar
{
    public function cloneVar($propertyValue): mixed
    {
        if (!\is_object($propertyValue)) {
            return $propertyValue;
        }

        $cloner = new VarCloner();
        $cloner->cloneVar($propertyValue);

        if ($propertyValue instanceof Collection) {
            $collectionItems = new ArrayCollection();

            foreach ($propertyValue as $item) {
                if (\is_object($item)) {
                    $item = clone $item;
                }
                $collectionItems->add($item);
            }

            return $collectionItems;
        }

        // Fallback to clone anyway
        return clone $propertyValue;
    }
}
