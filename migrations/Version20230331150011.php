<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230331150011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавляем поле template';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE content ADD template VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE content DROP template');
    }
}
