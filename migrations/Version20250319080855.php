<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250319080855 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE activities (
                name VARCHAR(255) NOT NULL,
                max_number_of_participants INT DEFAULT NULL,
                needed_equipment JSON DEFAULT \'[]\' NOT NULL,
                id VARCHAR(36) NOT NULL,
                description LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE activity_user (
                activity_id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                INDEX IDX_8E570DDB81C06096 (activity_id),
                INDEX IDX_8E570DDBA76ED395 (user_id),
                PRIMARY KEY(activity_id,
                        user_id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE attendee (
                name VARCHAR(255) NOT NULL,
                number_of_attendees INT NOT NULL,
                id VARCHAR(36) NOT NULL,
                scheduled_activity_id VARCHAR(36) NOT NULL,
                registered_by_id VARCHAR(36) NOT NULL,
                INDEX IDX_1150D56733E31936 (scheduled_activity_id),
                INDEX IDX_1150D56727E92E18 (registered_by_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE `booth` (
                max_number_of_participants INT DEFAULT NULL,
                available_equipment JSON DEFAULT \'[]\' NOT NULL,
                id VARCHAR(36) NOT NULL,
                name VARCHAR(255) NOT NULL,
                allow_attendee_registration TINYINT(1) DEFAULT 1 NOT NULL,
                map_image_data VARCHAR(255) DEFAULT NULL,
                map_width INT DEFAULT NULL,
                map_height INT DEFAULT NULL,
                map_mime_type VARCHAR(255) DEFAULT NULL,
                x_position INT DEFAULT 0 NOT NULL,
                y_position INT DEFAULT 0 NOT NULL,
                room_id VARCHAR(36) NOT NULL,
                INDEX IDX_D24EDE054177093 (room_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE event (
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                is_online_event TINYINT(1) NOT NULL,
                allow_activity_registration TINYINT(1) DEFAULT 1 NOT NULL,
                allow_attendee_registration TINYINT(1) DEFAULT 1 NOT NULL,
                locale VARCHAR(255) DEFAULT NULL,
                url VARCHAR(255) DEFAULT NULL,
                id VARCHAR(36) NOT NULL,
                description LONGTEXT NOT NULL,
                contact_name VARCHAR(255) DEFAULT NULL,
                contact_email VARCHAR(255) DEFAULT NULL,
                contact_phone VARCHAR(255) DEFAULT NULL,
                starts_at DATETIME NOT NULL,
                ends_at DATETIME NOT NULL,
                is_published TINYINT(1) DEFAULT 0 NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                venue_id VARCHAR(36) NOT NULL,
                INDEX IDX_3BAE0AA740A73EBA (venue_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE event_user (
                event_id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                INDEX IDX_92589AE271F7E88B (event_id),
                INDEX IDX_92589AE2A76ED395 (user_id),
                PRIMARY KEY(event_id, user_id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE floor (
                name VARCHAR(255) NOT NULL,
                id VARCHAR(36) NOT NULL,
                map_image_data VARCHAR(255) DEFAULT NULL,
                map_width INT DEFAULT NULL,
                map_height INT DEFAULT NULL,
                map_mime_type VARCHAR(255) DEFAULT NULL,
                venue_id VARCHAR(36) NOT NULL,
                x_position INT DEFAULT 0 NOT NULL,
                y_position INT DEFAULT 0 NOT NULL,
                INDEX IDX_BE45D62E40A73EBA (venue_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE reset_password_request (
                selector VARCHAR(20) NOT NULL,
                hashed_token VARCHAR(100) NOT NULL,
                requested_at DATETIME NOT NULL,
                expires_at DATETIME NOT NULL,
                id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                INDEX IDX_7CE748AA76ED395 (user_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE room (
                name VARCHAR(255) NOT NULL,
                id VARCHAR(36) NOT NULL,
                map_image_data VARCHAR(255) DEFAULT NULL,
                map_width INT DEFAULT NULL,
                map_height INT DEFAULT NULL,
                map_mime_type VARCHAR(255) DEFAULT NULL,
                x_position INT DEFAULT 0 NOT NULL,
                y_position INT DEFAULT 0 NOT NULL,
                floor_id VARCHAR(36) NOT NULL,
                INDEX IDX_729F519B854679E2 (floor_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE scheduled_activities (
                state VARCHAR(255) NOT NULL,
                id VARCHAR(36) NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                activity_id VARCHAR(36) NOT NULL,
                time_slot_id VARCHAR(36) NOT NULL,
                submitted_by VARCHAR(36) DEFAULT NULL,
                INDEX IDX_2B83275C81C06096 (activity_id),
                INDEX IDX_2B83275CD62B0FA (time_slot_id),
                INDEX IDX_2B83275C641EE842 (submitted_by),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE time_slot (
                is_open TINYINT(1) DEFAULT 1 NOT NULL,
                available_equipment JSON DEFAULT \'[]\' NOT NULL,
                id VARCHAR(36) NOT NULL,
                starts_at DATETIME NOT NULL,
                ends_at DATETIME NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                event_id VARCHAR(36) NOT NULL,
                booth_id VARCHAR(36) NOT NULL,
                INDEX IDX_1B3294A71F7E88B (event_id),
                INDEX IDX_1B3294A18707CED (booth_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE `user` (
                username VARCHAR(180) NOT NULL,
                email VARCHAR(255) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) NOT NULL,
                password_confirmation_token VARCHAR(255) DEFAULT NULL,
                email_confirmed_at DATETIME DEFAULT NULL,
                timezone VARCHAR(255) DEFAULT \'Europe/Paris\' NOT NULL,
                locale VARCHAR(255) DEFAULT \'fr\' NOT NULL,
                id VARCHAR(36) NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username),
                UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE venue (
                name VARCHAR(255) NOT NULL,
                id VARCHAR(36) NOT NULL,
                address1 VARCHAR(255) DEFAULT NULL,
                address2 VARCHAR(255) DEFAULT NULL,
                state VARCHAR(255) DEFAULT NULL,
                zip_code VARCHAR(255) DEFAULT NULL,
                city VARCHAR(255) DEFAULT NULL,
                country VARCHAR(255) DEFAULT NULL,
                latitude VARCHAR(255) DEFAULT NULL,
                longitude VARCHAR(255) DEFAULT NULL,
                contact_name VARCHAR(255) DEFAULT NULL,
                contact_email VARCHAR(255) DEFAULT NULL,
                contact_phone VARCHAR(255) DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                map_image_data VARCHAR(255) DEFAULT NULL,
                map_width INT DEFAULT NULL,
                map_height INT DEFAULT NULL,
                map_mime_type VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE venue_user (
                venue_id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                INDEX IDX_3BB5DBE140A73EBA (venue_id),
                INDEX IDX_3BB5DBE1A76ED395 (user_id),
                PRIMARY KEY(venue_id, user_id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE messenger_messages (
                id BIGINT AUTO_INCREMENT NOT NULL,
                body LONGTEXT NOT NULL,
                headers LONGTEXT NOT NULL,
                queue_name VARCHAR(190) NOT NULL,
                created_at DATETIME NOT NULL,
                available_at DATETIME NOT NULL,
                delivered_at DATETIME DEFAULT NULL,
                INDEX IDX_75EA56E0FB7336F0 (queue_name),
                INDEX IDX_75EA56E0E3BD61CE (available_at),
                INDEX IDX_75EA56E016BA31DB (delivered_at),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('ALTER TABLE activity_user ADD CONSTRAINT FK_8E570DDB81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_user ADD CONSTRAINT FK_8E570DDBA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D56733E31936 FOREIGN KEY (scheduled_activity_id) REFERENCES scheduled_activities (id)');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D56727E92E18 FOREIGN KEY (registered_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `booth` ADD CONSTRAINT FK_D24EDE054177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA740A73EBA FOREIGN KEY (venue_id) REFERENCES venue (id)');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE2A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E40A73EBA FOREIGN KEY (venue_id) REFERENCES venue (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B854679E2 FOREIGN KEY (floor_id) REFERENCES floor (id)');
        $this->addSql('ALTER TABLE scheduled_activities ADD CONSTRAINT FK_2B83275C81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE scheduled_activities ADD CONSTRAINT FK_2B83275CD62B0FA FOREIGN KEY (time_slot_id) REFERENCES time_slot (id)');
        $this->addSql('ALTER TABLE scheduled_activities ADD CONSTRAINT FK_2B83275C641EE842 FOREIGN KEY (submitted_by) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE time_slot ADD CONSTRAINT FK_1B3294A71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE time_slot ADD CONSTRAINT FK_1B3294A18707CED FOREIGN KEY (booth_id) REFERENCES `booth` (id)');
        $this->addSql('ALTER TABLE venue_user ADD CONSTRAINT FK_3BB5DBE140A73EBA FOREIGN KEY (venue_id) REFERENCES venue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE venue_user ADD CONSTRAINT FK_3BB5DBE1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
    }
}
