<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250213201431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename "Enabled" to "Published"';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event CHANGE enabled is_published TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
