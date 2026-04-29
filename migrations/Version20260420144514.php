<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260420144514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE time_slot ADD room_id VARCHAR(36)');
        $this->addSql('UPDATE time_slot JOIN booth ON time_slot.booth_id = booth.id SET time_slot.room_id = booth.room_id');
        $this->addSql('ALTER TABLE time_slot MODIFY room_id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE time_slot ADD CONSTRAINT FK_1B3294A54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('CREATE INDEX IDX_1B3294A54177093 ON time_slot (room_id)');
    }
}
