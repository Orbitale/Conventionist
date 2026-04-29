<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260412000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Phase 1 tables: Organizations, UserGroups, Game catalog, SafetyTool, Attendance, Notifications.';
    }

    public function up(Schema $schema): void
    {
        // Organization
        $this->addSql('
            CREATE TABLE organization (
                id VARCHAR(36) NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                website VARCHAR(255) DEFAULT NULL,
                logo_path VARCHAR(255) DEFAULT NULL,
                description LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_ORGANIZATION_SLUG (slug),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        $this->addSql('
            CREATE TABLE organization_user (
                organization_id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                INDEX IDX_ORG_USER_ORG (organization_id),
                INDEX IDX_ORG_USER_USER (user_id),
                PRIMARY KEY(organization_id, user_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        // UserGroup
        $this->addSql('
            CREATE TABLE user_group (
                id VARCHAR(36) NOT NULL,
                organization_id VARCHAR(36) NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_USER_GROUP_SLUG (slug),
                INDEX IDX_USER_GROUP_ORG (organization_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        $this->addSql('
            CREATE TABLE user_group_user (
                user_group_id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                INDEX IDX_UGU_GROUP (user_group_id),
                INDEX IDX_UGU_USER (user_id),
                PRIMARY KEY(user_group_id, user_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        // UserGroupMembership
        $this->addSql('
            CREATE TABLE user_group_membership (
                id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                user_group_id VARCHAR(36) NOT NULL,
                role VARCHAR(32) NOT NULL,
                joined_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                UNIQUE INDEX UNIQ_USER_GROUP_MEMBERSHIP (user_id, user_group_id),
                INDEX IDX_UGM_USER (user_id),
                INDEX IDX_UGM_GROUP (user_group_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        // UserExhibitionRole
        $this->addSql('
            CREATE TABLE user_exhibition_role (
                id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                event_id VARCHAR(36) NOT NULL,
                role VARCHAR(32) NOT NULL,
                scoped_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                UNIQUE INDEX UNIQ_USER_EVENT_ROLE (user_id, event_id, role),
                INDEX IDX_UER_USER (user_id),
                INDEX IDX_UER_EVENT (event_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        // GameCategory
        $this->addSql('
            CREATE TABLE game_categories (
                id VARCHAR(36) NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_GAME_CATEGORY_SLUG (slug),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        // GameTheme
        $this->addSql('
            CREATE TABLE game_themes (
                id VARCHAR(36) NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_GAME_THEME_SLUG (slug),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        // SafetyTool
        $this->addSql('
            CREATE TABLE safety_tools (
                id VARCHAR(36) NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description LONGTEXT NOT NULL,
                type VARCHAR(32) NOT NULL,
                content_warnings LONGTEXT DEFAULT \'\' NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_SAFETY_TOOL_SLUG (slug),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        // Game
        $this->addSql('
            CREATE TABLE games (
                id VARCHAR(36) NOT NULL,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description LONGTEXT NOT NULL,
                publisher VARCHAR(255) DEFAULT NULL,
                min_players INT DEFAULT NULL,
                max_players INT DEFAULT NULL,
                duration_minutes INT DEFAULT NULL,
                complexity VARCHAR(32) DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_GAME_SLUG (slug),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        $this->addSql('
            CREATE TABLE games_user (
                game_id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                INDEX IDX_GAMES_USER_GAME (game_id),
                INDEX IDX_GAMES_USER_USER (user_id),
                PRIMARY KEY(game_id, user_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        $this->addSql('
            CREATE TABLE games_categories (
                game_id VARCHAR(36) NOT NULL,
                game_category_id VARCHAR(36) NOT NULL,
                INDEX IDX_GAMES_CATS_GAME (game_id),
                INDEX IDX_GAMES_CATS_CAT (game_category_id),
                PRIMARY KEY(game_id, game_category_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        $this->addSql('
            CREATE TABLE games_themes (
                game_id VARCHAR(36) NOT NULL,
                game_theme_id VARCHAR(36) NOT NULL,
                INDEX IDX_GAMES_THEMES_GAME (game_id),
                INDEX IDX_GAMES_THEMES_THEME (game_theme_id),
                PRIMARY KEY(game_id, game_theme_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        // EventRegistration
        $this->addSql('
            CREATE TABLE event_registrations (
                id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                event_id VARCHAR(36) NOT NULL,
                status VARCHAR(32) NOT NULL,
                registered_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                confirmed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                notes LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_EVENT_REG_USER_EVENT (user_id, event_id),
                INDEX IDX_EVENT_REG_USER (user_id),
                INDEX IDX_EVENT_REG_EVENT (event_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        // EventRequest
        $this->addSql('
            CREATE TABLE event_requests (
                id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                event_id VARCHAR(36) NOT NULL,
                reviewed_by_id VARCHAR(36) DEFAULT NULL,
                request_type VARCHAR(32) NOT NULL,
                status VARCHAR(32) NOT NULL,
                message LONGTEXT DEFAULT NULL,
                reviewed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_EVENT_REQ_USER (user_id),
                INDEX IDX_EVENT_REQ_EVENT (event_id),
                INDEX IDX_EVENT_REQ_REVIEWER (reviewed_by_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        // Notification
        $this->addSql('
            CREATE TABLE notification (
                id VARCHAR(36) NOT NULL,
                recipient_id VARCHAR(36) NOT NULL,
                type VARCHAR(32) NOT NULL,
                level VARCHAR(16) NOT NULL,
                title VARCHAR(255) NOT NULL,
                body LONGTEXT NOT NULL,
                link VARCHAR(512) DEFAULT NULL,
                read_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                metadata JSON DEFAULT NULL COMMENT \'(DC2Type:json)\',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX idx_notification_recipient_read (recipient_id, read_at),
                INDEX IDX_NOTIFICATION_RECIPIENT (recipient_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB
        ');

        // Foreign keys
        $this->addSql('ALTER TABLE organization_user ADD CONSTRAINT FK_ORG_USER_ORG FOREIGN KEY (organization_id) REFERENCES organization (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_user ADD CONSTRAINT FK_ORG_USER_USER FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_USER_GROUP_ORG FOREIGN KEY (organization_id) REFERENCES organization (id)');

        $this->addSql('ALTER TABLE user_group_user ADD CONSTRAINT FK_UGU_GROUP FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_user ADD CONSTRAINT FK_UGU_USER FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE user_group_membership ADD CONSTRAINT FK_UGM_USER FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_membership ADD CONSTRAINT FK_UGM_GROUP FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE user_exhibition_role ADD CONSTRAINT FK_UER_USER FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_exhibition_role ADD CONSTRAINT FK_UER_EVENT FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE games_user ADD CONSTRAINT FK_GAMES_USER_GAME FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE games_user ADD CONSTRAINT FK_GAMES_USER_USER FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE games_categories ADD CONSTRAINT FK_GAMES_CATS_GAME FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE games_categories ADD CONSTRAINT FK_GAMES_CATS_CAT FOREIGN KEY (game_category_id) REFERENCES game_categories (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE games_themes ADD CONSTRAINT FK_GAMES_THEMES_GAME FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE games_themes ADD CONSTRAINT FK_GAMES_THEMES_THEME FOREIGN KEY (game_theme_id) REFERENCES game_themes (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE event_registrations ADD CONSTRAINT FK_EVENT_REG_USER FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE event_registrations ADD CONSTRAINT FK_EVENT_REG_EVENT FOREIGN KEY (event_id) REFERENCES event (id)');

        $this->addSql('ALTER TABLE event_requests ADD CONSTRAINT FK_EVENT_REQ_USER FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE event_requests ADD CONSTRAINT FK_EVENT_REQ_EVENT FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event_requests ADD CONSTRAINT FK_EVENT_REQ_REVIEWER FOREIGN KEY (reviewed_by_id) REFERENCES `user` (id)');

        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_NOTIFICATION_RECIPIENT FOREIGN KEY (recipient_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop FKs first (drop tables in reverse dependency order)
        $this->addSql('DROP TABLE IF EXISTS notification');
        $this->addSql('DROP TABLE IF EXISTS event_requests');
        $this->addSql('DROP TABLE IF EXISTS event_registrations');
        $this->addSql('DROP TABLE IF EXISTS games_themes');
        $this->addSql('DROP TABLE IF EXISTS games_categories');
        $this->addSql('DROP TABLE IF EXISTS games_user');
        $this->addSql('DROP TABLE IF EXISTS games');
        $this->addSql('DROP TABLE IF EXISTS safety_tools');
        $this->addSql('DROP TABLE IF EXISTS game_themes');
        $this->addSql('DROP TABLE IF EXISTS game_categories');
        $this->addSql('DROP TABLE IF EXISTS user_exhibition_role');
        $this->addSql('DROP TABLE IF EXISTS user_group_membership');
        $this->addSql('DROP TABLE IF EXISTS user_group_user');
        $this->addSql('DROP TABLE IF EXISTS user_group');
        $this->addSql('DROP TABLE IF EXISTS organization_user');
        $this->addSql('DROP TABLE IF EXISTS organization');
    }
}
