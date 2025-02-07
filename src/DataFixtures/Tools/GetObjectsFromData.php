<?php

namespace App\DataFixtures\Tools;

use Doctrine\Common\Collections\ArrayCollection;

trait GetObjectsFromData
{
    abstract public static function getStaticData(): array;

    public static function getIdFromName(string $name): string
    {
        foreach (static::getStaticData() as $id => $values) {
            if ($values['name'] === $name) {
                return $id;
            }
        }

        throw new \RuntimeException(\sprintf(
            'No fixture object with name "%s" in class "%s".',
            $name,
            static::class
        ));
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
                            $arrayData[$key] =  $this->getReference($arrayValue->name, $arrayValue->class);
                            $hasRef = true;
                        }
                    }

                    $value = $hasRef ? new ArrayCollection($arrayData) : $arrayData;
                }

                $properties[$property] = $value;
            }

            yield $id => $properties;
        }
    }
}
