<?php

namespace App\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait Position
{
    #[ORM\Column(name: 'x_position', type: Types::INTEGER, nullable: false, options: ['default' => 0])]
    private int $xPosition = 0;

    #[ORM\Column(name: 'y_position', type: Types::INTEGER, nullable: false, options: ['default' => 0])]
    private int $yPosition = 0;

    public function getXPosition(): int
    {
        return $this->xPosition;
    }

    public function setXPosition(int $xPosition): void
    {
        $this->xPosition = $xPosition;
    }

    public function getYPosition(): int
    {
        return $this->yPosition;
    }

    public function setYPosition(int $yPosition): void
    {
        $this->yPosition = $yPosition;
    }
}