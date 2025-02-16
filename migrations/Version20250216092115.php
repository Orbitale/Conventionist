<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250216092115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activities ADD needed_equipment JSON NOT NULL COMMENT "(DC2Type:json)"');
        $this->addSql('ALTER TABLE booth ADD available_equipment JSON NOT NULL COMMENT "(DC2Type:json)"');
        $this->addSql('ALTER TABLE time_slot ADD available_equipment JSON NOT NULL COMMENT "(DC2Type:json)"');
    }

    public function down(Schema $schema): void
    {
    }
}
