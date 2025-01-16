<?php

namespace App\DataFixtures;

use App\Entity\TimeSlotCategory;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

class TimeSlotCategoryFixture extends ArrayFixture implements ORMFixtureInterface
{
    protected function getEntityClass(): string
    {
        return TimeSlotCategory::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'time-slot-category-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }

    protected function getObjects(): iterable
    {
        return [
            [
                'id' => 'a798fa20-c6ae-465e-aec2-45727a4a3dba',
                'name' => 'Morning',
                'description' => '',
            ],
            [
                'id' => '936a121d-38da-4b5b-8bd7-88823fd66d5e',
                'name' => 'Afternoon',
                'description' => '',
            ],
            [
                'id' => 'efde61d3-5f6e-45af-97b3-bfb4b6387d99',
                'name' => 'Evening',
                'description' => '',
            ],
            [
                'id' => '73a2c621-f28f-4219-9395-aea5e3b04947',
                'name' => 'Night',
                'description' => '',
            ],
        ];
    }
}
