<?php

namespace App\Controller\Admin;

use App\Entity\Floor;
use App\Entity\HasMapImage;
use App\Entity\Room;
use App\Entity\Venue;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SaveRoomDispositionController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $registry,
    ) {
    }

    #[Route('/admin/maps/{entity}/{id}/save-disposition', name: 'admin_map_save_disposition', methods: ['POST'])]
    public function saveRoomPositions(
        string $entity,
        string $id,
        Request $request,
    ): JsonResponse {
        try {
            $managersToFlush = [];

            $class = $this->getEntityClass($entity);
            dump($entity, $class, $id);
            /** @var HasMapImage $parentEntity */
            $parentEntity = $this->registry->getRepository($class)->find($id);

            if (!$parentEntity) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Entity not found'
                ], 404);
            }
            if (!$parentEntity instanceof HasMapImage) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Entity type does not implement the HasMapImage interface.',
                ], 404);
            }
            if (!$parentEntity->getChildrenClass()) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Entity type does cannot have children.',
                ], 404);
            }

            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $positions = $data['positions'] ?? [];

            if (empty($positions)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'No positions provided'
                ], 400);
            }

            $childClass = $parentEntity->getChildrenClass();
            $childrenRepository = $this->registry->getRepository($childClass);
            $updatedCount = 0;

            foreach ($positions as $position) {
                $childId = $position['id'] ?? null;
                $x = $position['x'] ?? null;
                $y = $position['y'] ?? null;

                if ($childId === null || $x === null || $y === null) {
                    return new JsonResponse([
                        'success' => false,
                        'error' => 'Invalid positions provided: each position must have an entity ID, and X and Y coordinates.'
                    ], 400);
                }

                $child = $childrenRepository->find($childId);

                if (!$child) {
                    return new JsonResponse([
                        'success' => false,
                        'error' => 'Child ID not found.',
                    ], 400);
                }

                $child->setXPosition((int) $x);
                $child->setYPosition((int) $y);
                $manager = $this->registry->getManagerForClass($childClass);
                if (!$manager) {
                    return new JsonResponse([
                        'success' => false,
                        'error' => 'Cannot save child, manager not found.',
                    ], 400);
                }
                $manager->persist($child);
                $updatedCount++;
                if (!in_array($manager, $managersToFlush, true)) {
                    $managersToFlush[] = $manager;
                }
            }

            foreach ($managersToFlush as $manager) {
                $manager->flush();
            }

            return new JsonResponse([
                'success' => true,
                'message' => "Updated {$updatedCount} room position(s)",
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @return class-string<HasMapImage>
     */
    private function getEntityClass(string $entity): string
    {
        if ($entity === 'venue') {
            return Venue::class;
        }
        if ($entity === 'floor') {
            return Floor::class;
        }
        if ($entity === 'room') {
            return Room::class;
        }

        throw new \RuntimeException(\sprintf("Cannot determine entity class from name \"%s\".", $entity));
    }
}
