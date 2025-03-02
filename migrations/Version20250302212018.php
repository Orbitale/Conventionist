<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class Version20250302212018 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $slugger = new AsciiSlugger();

        $result = $this->connection->fetchAllAssociative('SELECT id, name FROM event WHERE slug IS NULL or slug = ""');
        foreach ($result as $row) {
            $this->addSql('UPDATE event SET slug = :slug WHERE id = :id', [
                'slug' => $slugger->slug($row['name'])->toString(),
                'id' => $row['id'],
            ]);
        }
    }

    public function down(Schema $schema): void
    {
    }
}
