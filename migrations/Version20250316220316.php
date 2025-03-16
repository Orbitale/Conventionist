<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250316220316 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event CHANGE allow_activity_registration allow_activity_registration TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE event CHANGE allow_attendee_registration allow_attendee_registration TINYINT(1) DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
