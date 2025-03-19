<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250319074706 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE scheduled_activities CHANGE submitted_by submitted_by VARCHAR(36) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
