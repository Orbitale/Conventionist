<?php

declare(strict_types=1);


namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260427083105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add TimeSlot::$room property';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_slot ADD room_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('UPDATE time_slot JOIN booth ON time_slot.booth_id = booth.id SET time_slot.room_id = booth.room_id');
        $this->addSql('ALTER TABLE time_slot CHANGE room_id room_id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE time_slot CHANGE booth_id booth_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE time_slot ADD CONSTRAINT FK_1B3294A54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('CREATE INDEX IDX_1B3294A54177093 ON time_slot (room_id)');
    }

    public function down(Schema $schema): void
    {
    }
}
