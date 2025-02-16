<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250213101326 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table` RENAME booth');
        $this->addSql('ALTER TABLE time_slot CHANGE table_id booth_id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE booth RENAME INDEX idx_f6298f4654177093 TO IDX_D24EDE054177093');
        $this->addSql('ALTER TABLE time_slot RENAME INDEX idx_1b3294aecff285c TO IDX_1B3294A18707CED');
    }

    public function down(Schema $schema): void
    {
    }
}
