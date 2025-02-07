<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250207105709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Previous locale was fr_FR, not necessary to have this level of precision (yet)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user CHANGE locale locale VARCHAR(255) DEFAULT \'fr\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
