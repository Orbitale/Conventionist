<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250217221901 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activities CHANGE needed_equipment needed_equipment JSON DEFAULT "[]" NOT NULL COMMENT "(DC2Type:json)"');
        $this->addSql('ALTER TABLE booth CHANGE available_equipment available_equipment JSON DEFAULT "[]" NOT NULL COMMENT "(DC2Type:json)"');
        $this->addSql('ALTER TABLE time_slot CHANGE available_equipment available_equipment JSON DEFAULT "[]" NOT NULL COMMENT "(DC2Type:json)"');
    }

    public function down(Schema $schema): void
    {
    }
}
