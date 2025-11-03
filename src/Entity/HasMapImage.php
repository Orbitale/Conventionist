<?php

namespace App\Entity;

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

    /**
     * @return array<HasMapImage>
     */
    public function getChildren(): array;

    /** @return null|class-string<HasMapImage> */
    public function getChildrenClass(): ?string;
}
