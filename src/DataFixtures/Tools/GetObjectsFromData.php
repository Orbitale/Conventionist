<?php

namespace App\DataFixtures\Tools;

use Doctrine\Common\Collections\ArrayCollection;

trait GetObjectsFromData
{
    abstract public static function getStaticData(): iterable;

    public static function filterByKeyAndValue(string $key, mixed $value): array
    {
        return \array_filter(self::getStaticData(), static fn (array $data) => $data[$key] === $value);
    }

    public static function filterData(\Closure $callback): array
    {
        return \array_filter(self::getStaticData(), $callback);
    }

    public static function getIdFromName(string $name): string
    {
        foreach (static::getStaticData() as $id => $values) {
            if ($values['name'] === $name) {
                return $id;
            }
        }

        throw new \RuntimeException(\sprintf('No fixture object with name "%s" in class "%s".', $name, static::class));
    }

    protected function getObjects(): iterable
    {
        foreach (static::getStaticData() as $id => $properties) {
            foreach ($properties as $property => $value) {
                $properties['id'] = $id;

                if ($value instanceof \Closure) {
                    $value = $value();
                }

                if ($value instanceof Ref) {
                    $value = $this->getReference($value->name, $value->class);
                } elseif (\is_array($value)) {
                    $arrayData = $value;
                    $hasRef = false;

                    foreach ($arrayData as $key => $arrayValue) {
                        if ($arrayValue instanceof Ref) {
                            $arrayData[$key] = $this->getReference($arrayValue->name, $arrayValue->class);
                            $hasRef = true;
                        }
                    }

                    $value = $hasRef ? new ArrayCollection($arrayData) : $arrayData;
                }

                $properties[$property] = $value;
            }

            $class = $this->getEntityClass();
            if (\property_exists($class, 'createdAt')) {
                $properties['createdAt'] = new \DateTime();
            }
            if (\property_exists($class, 'updatedAt')) {
                $properties['updatedAt'] = new \DateTime();
            }

            yield $id => $properties;
        }
    }
}
