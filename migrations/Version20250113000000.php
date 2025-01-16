<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250113000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE animation (
          id VARCHAR(36) NOT NULL,
          name VARCHAR(255) NOT NULL,
          max_number_of_participants INT NOT NULL,
          description LONGTEXT NOT NULL,
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE animation_user (
          animation_id VARCHAR(36) NOT NULL,
          user_id VARCHAR(36) NOT NULL,
          INDEX IDX_1F9D09BE3858647E (animation_id),
          INDEX IDX_1F9D09BEA76ED395 (user_id),
          PRIMARY KEY(animation_id, user_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE attendee (
          id VARCHAR(36) NOT NULL,
          scheduled_animation_id VARCHAR(36) NOT NULL,
          registered_by_id VARCHAR(36) NOT NULL,
          name VARCHAR(255) NOT NULL,
          number_of_attendees INT NOT NULL,
          INDEX IDX_1150D567EE3CE4EF (scheduled_animation_id),
          INDEX IDX_1150D56727E92E18 (registered_by_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE event (
          id VARCHAR(36) NOT NULL,
          venue_id VARCHAR(36) NOT NULL,
          name VARCHAR(255) NOT NULL,
          address LONGTEXT NOT NULL,
          is_online_event TINYINT(1) NOT NULL,
          description LONGTEXT NOT NULL,
          starts_at DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)",
          ends_at DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)",
          enabled TINYINT(1) DEFAULT 0 NOT NULL,
          INDEX IDX_3BAE0AA740A73EBA (venue_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE event_user (
          event_id VARCHAR(36) NOT NULL,
          user_id VARCHAR(36) NOT NULL,
          INDEX IDX_92589AE271F7E88B (event_id),
          INDEX IDX_92589AE2A76ED395 (user_id),
          PRIMARY KEY(event_id, user_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE floor (
          id VARCHAR(36) NOT NULL,
          venue_id VARCHAR(36) NOT NULL,
          name VARCHAR(255) NOT NULL,
          INDEX IDX_BE45D62E40A73EBA (venue_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE reset_password_request (
          id VARCHAR(36) NOT NULL,
          user_id VARCHAR(36) NOT NULL,
          selector VARCHAR(20) NOT NULL,
          hashed_token VARCHAR(100) NOT NULL,
          requested_at DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)",
          expires_at DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)",
          INDEX IDX_7CE748AA76ED395 (user_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE room (
          id VARCHAR(36) NOT NULL,
          floor_id VARCHAR(36) NOT NULL,
          name VARCHAR(255) NOT NULL,
          INDEX IDX_729F519B854679E2 (floor_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE scheduled_animation (
          id VARCHAR(36) NOT NULL,
          animation_id VARCHAR(36) NOT NULL,
          time_slot_id VARCHAR(36) NOT NULL,
          state VARCHAR(255) NOT NULL,
          INDEX IDX_9B4F9EDB3858647E (animation_id),
          INDEX IDX_9B4F9EDBD62B0FA (time_slot_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE `table` (
          id VARCHAR(36) NOT NULL,
          room_id VARCHAR(36) NOT NULL,
          name VARCHAR(255) NOT NULL,
          max_number_of_participants INT DEFAULT NULL,
          INDEX IDX_F6298F4654177093 (room_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE time_slot (
          id VARCHAR(36) NOT NULL,
          category_id VARCHAR(36) DEFAULT NULL,
          event_id VARCHAR(36) DEFAULT NULL,
          table_id VARCHAR(36) NOT NULL,
          starts_at DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)",
          ends_at DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)",
          INDEX IDX_1B3294A12469DE2 (category_id),
          INDEX IDX_1B3294A71F7E88B (event_id),
          INDEX IDX_1B3294AECFF285C (table_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE time_slot_category (
          id VARCHAR(36) NOT NULL,
          name VARCHAR(255) NOT NULL,
          description LONGTEXT NOT NULL,
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE `user` (
          id VARCHAR(36) NOT NULL,
          username VARCHAR(180) NOT NULL,
          email VARCHAR(255) NOT NULL,
          roles JSON NOT NULL COMMENT "(DC2Type:json)",
          password VARCHAR(255) NOT NULL,
          password_confirmation_token VARCHAR(255) DEFAULT NULL,
          is_email_confirmed TINYINT(1) DEFAULT 0 NOT NULL,
          is_verified TINYINT(1) DEFAULT 0 NOT NULL,
          timezone VARCHAR(255) DEFAULT "Europe/Paris" NOT NULL,
          locale VARCHAR(255) DEFAULT "fr_FR" NOT NULL,
          UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username),
          UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE venue (
          id VARCHAR(36) NOT NULL,
          name VARCHAR(255) NOT NULL,
          address LONGTEXT NOT NULL,
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE venue_user (
          venue_id VARCHAR(36) NOT NULL,
          user_id VARCHAR(36) NOT NULL,
          INDEX IDX_3BB5DBE140A73EBA (venue_id),
          INDEX IDX_3BB5DBE1A76ED395 (user_id),
          PRIMARY KEY(venue_id, user_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE messenger_messages (
          id BIGINT AUTO_INCREMENT NOT NULL,
          body LONGTEXT NOT NULL,
          headers LONGTEXT NOT NULL,
          queue_name VARCHAR(190) NOT NULL,
          created_at DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)",
          available_at DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)",
          delivered_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)",
          INDEX IDX_75EA56E0FB7336F0 (queue_name),
          INDEX IDX_75EA56E0E3BD61CE (available_at),
          INDEX IDX_75EA56E016BA31DB (delivered_at),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE animation_user ADD CONSTRAINT FK_1F9D09BE3858647E FOREIGN KEY (animation_id) REFERENCES animation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animation_user ADD CONSTRAINT FK_1F9D09BEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D567EE3CE4EF FOREIGN KEY (scheduled_animation_id) REFERENCES scheduled_animation (id)');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D56727E92E18 FOREIGN KEY (registered_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA740A73EBA FOREIGN KEY (venue_id) REFERENCES venue (id)');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE2A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E40A73EBA FOREIGN KEY (venue_id) REFERENCES venue (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B854679E2 FOREIGN KEY (floor_id) REFERENCES floor (id)');
        $this->addSql('ALTER TABLE scheduled_animation ADD CONSTRAINT FK_9B4F9EDB3858647E FOREIGN KEY (animation_id) REFERENCES animation (id)');
        $this->addSql('ALTER TABLE scheduled_animation ADD CONSTRAINT FK_9B4F9EDBD62B0FA FOREIGN KEY (time_slot_id) REFERENCES time_slot (id)');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F4654177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE time_slot ADD CONSTRAINT FK_1B3294A12469DE2 FOREIGN KEY (category_id) REFERENCES time_slot_category (id)');
        $this->addSql('ALTER TABLE time_slot ADD CONSTRAINT FK_1B3294A71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE time_slot ADD CONSTRAINT FK_1B3294AECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE venue_user ADD CONSTRAINT FK_3BB5DBE140A73EBA FOREIGN KEY (venue_id) REFERENCES venue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE venue_user ADD CONSTRAINT FK_3BB5DBE1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
    }
}
