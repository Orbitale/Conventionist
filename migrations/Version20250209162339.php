<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250209162339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Event::$slug, remove TimeSlotCategory (for now), add Timestampable on many entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_slot DROP FOREIGN KEY FK_1B3294A12469DE2');
        $this->addSql('DROP TABLE time_slot_category');
        $this->addSql('ALTER TABLE animation ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE event ADD slug VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE scheduled_animation ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('DROP INDEX IDX_1B3294A12469DE2 ON time_slot');
        $this->addSql('ALTER TABLE time_slot ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, DROP category_id');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE venue ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
