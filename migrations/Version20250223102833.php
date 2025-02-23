<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250223102833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'After DBAL 4 upgrade, remove Doctrine comments from schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activities CHANGE needed_equipment needed_equipment JSON DEFAULT \'[]\' NOT NULL');
        $this->addSql('ALTER TABLE booth CHANGE available_equipment available_equipment JSON DEFAULT \'[]\' NOT NULL');
        $this->addSql('ALTER TABLE event CHANGE starts_at starts_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE event CHANGE ends_at ends_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE reset_password_request CHANGE requested_at requested_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE reset_password_request CHANGE expires_at expires_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE time_slot CHANGE starts_at starts_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE time_slot CHANGE ends_at ends_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE time_slot CHANGE available_equipment available_equipment JSON DEFAULT \'[]\' NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE email_confirmed_at email_confirmed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE available_at available_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
