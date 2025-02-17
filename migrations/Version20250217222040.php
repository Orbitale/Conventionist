<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250217222040 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE activities SET needed_equipment = "[]" WHERE needed_equipment IS NULL');
        $this->addSql('UPDATE booth SET available_equipment = "[]" WHERE available_equipment IS NULL');
        $this->addSql('UPDATE time_slot SET available_equipment = "[]" WHERE available_equipment IS NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
