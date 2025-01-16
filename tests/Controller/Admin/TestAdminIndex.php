<?php

namespace App\Tests\Controller\Admin;

use App\DataFixtures\Tools\Ref;
use App\Tests\GetUser;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;

trait TestAdminIndex
{
    abstract protected static function getIndexColumnNames(): array;

    protected function runIndexPage(array $indexData): void
    {
        if (!$this instanceof AbstractCrudTestCase) {
            throw new \RuntimeException(\sprintf('Trait "%s" used by class "%s" can only be used in an instance of "%s".', self::class, static::class, AbstractCrudTestCase::class));
        }
        if (!isset(\class_uses(static::class)[GetUser::class])) {
            throw new \RuntimeException(\sprintf('Trait "%s" used by class "%s" can only be used when using the "%s" trait.', self::class, static::class, GetUser::class));
        }

        $this->client->loginUser($this->getUser());
        $this->client->request("GET", $this->generateIndexUrl());

        static::assertResponseIsSuccessful();
        static::assertIndexFullEntityCount(\count($indexData));
        $this->assertIndexPageEntityCount(\count($indexData));

        foreach ($indexData as $id => $data) {
            $row = $this->client->getCrawler()->filter($this->getIndexEntityRowSelector($id));
            foreach (self::getIndexColumnNames() as $column) {
                $value = $data[$column];

                if ($value instanceof Ref) {
                    // Don't check references: they come from another Crud and might be formatted
                    continue;
                }

                if (null === $value) {
                    $value = 'Null';
                }

                static::assertSame((string) $value, $row->filter($this->getIndexColumnSelector($column, 'data'))->text());
            }
        }
    }
}
