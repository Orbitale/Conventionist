<?php

namespace App\Entity\Field;

use App\Entity\Room;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait MapImage
{
    #[ORM\Column(name: 'map_image_data', type: Types::STRING, length: 255, nullable: true)]
    private ?string $mapImage = null;

    #[ORM\Column(name: 'map_width', type: Types::INTEGER, nullable: true)]
    private ?int $mapWidth = null;

    #[ORM\Column(name: 'map_height', type: Types::INTEGER, nullable: true)]
    private ?int $mapHeight = null;

    #[ORM\Column(name: 'map_mime_type', type: Types::STRING, nullable: true)]
    private ?string $mapMimeType = null;

    public function getMapImage(): ?string
    {
        return $this->mapImage;
    }

    public function setMapImage(?string $mapImage): void
    {
        $this->mapImage = $mapImage;
    }

    public function getMapWidth(): ?int
    {
        return $this->mapWidth;
    }

    public function setMapWidth(?int $mapWidth): void
    {
        $this->mapWidth = $mapWidth;
    }

    public function getMapHeight(): ?int
    {
        return $this->mapHeight;
    }

    public function setMapHeight(?int $mapHeight): void
    {
        $this->mapHeight = $mapHeight;
    }

    public function getMapMimeType(): ?string
    {
        return $this->mapMimeType;
    }

    public function setMapMimeType(?string $mapMimeType): void
    {
        $this->mapMimeType = $mapMimeType;
    }

    public function hasMapData(): bool
    {
        return $this->mapImage !== null && $this->mapWidth !== null && $this->mapHeight !== null;
    }

    public function getMapJson(): array
    {
        if (!$this->hasMapData()) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'imageUrl' => $this->mapImage,
            'width' => $this->mapWidth,
            'height' => $this->mapHeight,
            'mimeType' => $this->mapMimeType,
            'rooms' => array_map(static fn(Room $room) => $room->getMapJson(), $this->rooms->toArray()),
        ];
    }
}
