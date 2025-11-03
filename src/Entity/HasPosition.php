<?php

namespace App\Entity;

interface HasPosition
{
    public function getXPosition(): int;

    public function setXPosition(int $xPosition): void;

    public function getYPosition(): int;

    public function setYPosition(int $yPosition): void;
}
