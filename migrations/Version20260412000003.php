<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260412000003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Phase 3: extend event and scheduled_activities with visibility, capacity, waitlist, GM check-in, game/safety-tool links.';
    }

    public function up(Schema $schema): void
    {
        // Event
        $this->addSql('
            ALTER TABLE event
                ADD visibility VARCHAR(32) DEFAULT \'draft\' NOT NULL,
                ADD registration_opens_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                ADD registration_closes_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                ADD capacity INT DEFAULT NULL,
                ADD waitlist_enabled TINYINT(1) DEFAULT 0 NOT NULL,
                ADD `website` VARCHAR(255) DEFAULT NULL,
                ADD cover_image_path VARCHAR(255) DEFAULT NULL,
                ADD organization_id VARCHAR(36) DEFAULT NULL
        ');
        $this->addSql('CREATE INDEX IDX_EVENT_ORGANIZATION ON event (organization_id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_EVENT_ORGANIZATION FOREIGN KEY (organization_id) REFERENCES organization (id) ON DELETE SET NULL');

        // ScheduledActivity
        $this->addSql('
            ALTER TABLE scheduled_activities
                ADD capacity INT DEFAULT NULL,
                ADD waitlist_enabled TINYINT(1) DEFAULT 0 NOT NULL,
                ADD gm_checked_in_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                ADD grace_deadline_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                ADD game_id VARCHAR(36) DEFAULT NULL
        ');
        $this->addSql('CREATE INDEX IDX_SCHEDULED_ACTIVITY_GAME ON scheduled_activities (game_id)');
        $this->addSql('ALTER TABLE scheduled_activities ADD CONSTRAINT FK_SCHEDULED_ACTIVITY_GAME FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE SET NULL');

        // Join table for ScheduledActivity <-> SafetyTool
        $this->addSql('
            CREATE TABLE scheduled_activity_safety_tool (
                scheduled_activity_id VARCHAR(36) NOT NULL,
                safety_tool_id VARCHAR(36) NOT NULL,
                INDEX IDX_SA_ST_ACTIVITY (scheduled_activity_id),
                INDEX IDX_SA_ST_TOOL (safety_tool_id),
                PRIMARY KEY(scheduled_activity_id, safety_tool_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');
        $this->addSql('ALTER TABLE scheduled_activity_safety_tool ADD CONSTRAINT FK_SA_ST_ACTIVITY FOREIGN KEY (scheduled_activity_id) REFERENCES scheduled_activities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scheduled_activity_safety_tool ADD CONSTRAINT FK_SA_ST_TOOL FOREIGN KEY (safety_tool_id) REFERENCES safety_tools (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS scheduled_activity_safety_tool');

        $this->addSql('ALTER TABLE scheduled_activities DROP FOREIGN KEY FK_SCHEDULED_ACTIVITY_GAME');
        $this->addSql('DROP INDEX IDX_SCHEDULED_ACTIVITY_GAME ON scheduled_activities');
        $this->addSql('
            ALTER TABLE scheduled_activities
                DROP capacity,
                DROP waitlist_enabled,
                DROP gm_checked_in_at,
                DROP grace_deadline_at,
                DROP game_id
        ');

        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_EVENT_ORGANIZATION');
        $this->addSql('DROP INDEX IDX_EVENT_ORGANIZATION ON event');
        $this->addSql('
            ALTER TABLE event
                DROP visibility,
                DROP registration_opens_at,
                DROP registration_closes_at,
                DROP capacity,
                DROP waitlist_enabled,
                DROP `website`,
                DROP cover_image_path,
                DROP organization_id
        ');
    }
}
