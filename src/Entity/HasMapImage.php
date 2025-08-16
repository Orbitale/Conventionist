<?php

namespace App\Entity;

use App\Validator\ValidateMapImage;

#[ValidateMapImage]
interface HasMapImage
{
    public function hasMapData(): bool;

    public function getMapImage(): ?string;

    public function setMapImage(?string $mapImage): void;

    public function getMapWidth(): ?int;

    public function setMapWidth(?int $mapWidth): void;

    public function getMapHeight(): ?int;

    public function setMapHeight(?int $mapHeight): void;

    public function getMapMimeType(): ?string;

    public function setMapMimeType(?string $mapMimeType): void;
}
