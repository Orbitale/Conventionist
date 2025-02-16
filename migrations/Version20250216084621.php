<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250216084621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename "animation" to "activity"';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE animation RENAME activities');

        $this->addSql('ALTER TABLE animation_user RENAME activity_user');
        $this->addSql('DROP INDEX `primary` ON activity_user');
        $this->addSql('ALTER TABLE activity_user CHANGE animation_id activity_id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE activity_user ADD PRIMARY KEY (activity_id, user_id)');
        $this->addSql('ALTER TABLE activity_user RENAME INDEX idx_1f9d09be3858647e TO IDX_8E570DDB81C06096;');
        $this->addSql('ALTER TABLE activity_user RENAME INDEX idx_1f9d09bea76ed395 TO IDX_8E570DDBA76ED395;');

        $this->addSql('ALTER TABLE scheduled_animation RENAME scheduled_activities');
        $this->addSql('ALTER TABLE scheduled_activities RENAME INDEX idx_9b4f9edb3858647e TO IDX_2B83275C81C06096;');
        $this->addSql('ALTER TABLE scheduled_activities RENAME INDEX idx_9b4f9edbd62b0fa TO IDX_2B83275CD62B0FA;');
        $this->addSql('ALTER TABLE scheduled_activities CHANGE animation_id activity_id VARCHAR(36) NOT NULL');

        $this->addSql('ALTER TABLE attendee DROP FOREIGN KEY FK_1150D567EE3CE4EF;');
        $this->addSql('DROP INDEX IDX_1150D567EE3CE4EF ON attendee;');
        $this->addSql('ALTER TABLE attendee CHANGE scheduled_animation_id scheduled_activity_id VARCHAR(36) NOT NULL;');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D56733E31936 FOREIGN KEY (scheduled_activity_id) REFERENCES scheduled_activities (id);');
        $this->addSql('CREATE INDEX IDX_1150D56733E31936 ON attendee (scheduled_activity_id);');
    }

    public function down(Schema $schema): void
    {
    }
}
