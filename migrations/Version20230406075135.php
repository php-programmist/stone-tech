<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230406075135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавляем короткие названия категорий';
    }

    public function up(Schema $schema): void
    {

        $this->addSql('update category set short_name = ? where name = ?',[
            'Столешницы для кухни',
            'Столешницы из мрамора для кухни',
        ]);

        $this->addSql('update category set short_name = ? where name = ?',[
            'Столешницы для ванной комнаты',
            'Столешницы из мрамора для ванной комнаты',
        ]);

    }

    public function down(Schema $schema): void
    {

    }
}
