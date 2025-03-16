<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250312074945 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event ADD allow_activity_registration TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD allow_attendee_registration TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
