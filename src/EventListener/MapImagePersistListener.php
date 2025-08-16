<?php

namespace App\EventListener;

use App\Entity\HasMapImage;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Event\EntityLifecycleEventInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class MapImagePersistListener implements EventSubscriberInterface
{
    public function __construct(
        private string $publicDir,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityUpdatedEvent::class => 'updateImageSize',
            BeforeEntityPersistedEvent::class => 'updateImageSize',
        ];
    }

    public function updateImageSize(EntityLifecycleEventInterface $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof HasMapImage) {
            return;
        }

        if (!$entity->getMapImage()) {
            return;
        }

        $imagePath = \rtrim($this->publicDir, '/') . '/' . \ltrim($entity->getMapImage(), '/');
        if (!file_exists($imagePath)) {
            throw new \RuntimeException('Cannot load image: file does not exist.');
        }

        $size = @getimagesize($imagePath);
        if (!$size || empty($size[0]) || empty($size[1])) {
            throw new \RuntimeException('Cannot determine image size.');
        }
        if (!isset($size['mime'])) {
            throw new \RuntimeException('Cannot determine image type.');
        }

        $entity->setMapWidth($size[0]);
        $entity->setMapHeight($size[1]);
        $entity->setMapMimeType($size['mime']);
    }
}
