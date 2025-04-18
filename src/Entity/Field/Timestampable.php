<?php

namespace App\Entity\Field;

use Gedmo\Timestampable\Traits\TimestampableEntity;

trait Timestampable
{
    use TimestampableEntity;

    public function generateTimestamps(): void
    {
        if (
            !\property_exists($this, 'createdAt')
            || !\property_exists($this, 'updatedAt')
        ) {
            throw new \RuntimeException(\sprintf('Wrong usage of "%s" trait: it must use TimestampableEntity, or at least contain the same properties.', self::class));
        }

        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }
}
