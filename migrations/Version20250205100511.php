<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250205100511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make event not null in TimeSlot';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_slot CHANGE event_id event_id VARCHAR(36) NOT NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
