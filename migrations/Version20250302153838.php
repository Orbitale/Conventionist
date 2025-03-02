<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250302153838 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event ADD locale VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD contact_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD contact_email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD contact_phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event DROP address');

        $this->addSql('ALTER TABLE venue ADD address1 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE venue ADD address2 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE venue ADD state VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE venue ADD zip_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE venue ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE venue ADD country VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE venue ADD latitude VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE venue ADD longitude VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE venue ADD contact_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE venue ADD contact_email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE venue ADD contact_phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE venue DROP address');
    }

    public function down(Schema $schema): void
    {
    }
}
