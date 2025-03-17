<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250318075224 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE scheduled_activities ADD submitted_by VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE scheduled_activities ADD CONSTRAINT FK_2B83275C641EE842 FOREIGN KEY (submitted_by) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_2B83275C641EE842 ON scheduled_activities (submitted_by)');
    }

    public function down(Schema $schema): void
    {
    }
}
