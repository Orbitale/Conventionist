<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260412000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add birth_date, privacy_accepted_at, theme_preference columns to user.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE `user`
                ADD birth_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\',
                ADD privacy_accepted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                ADD theme_preference VARCHAR(16) DEFAULT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE `user`
                DROP birth_date,
                DROP privacy_accepted_at,
                DROP theme_preference
        ');
    }
}
