<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250213213539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add TimeSlot::$open';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_slot ADD is_open TINYINT(1) DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
