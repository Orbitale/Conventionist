<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250209101434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make Animation::$maxNumberOfParticipants nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE animation CHANGE max_number_of_participants max_number_of_participants INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
