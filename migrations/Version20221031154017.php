<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221031154017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE content_element (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dt_create DATETIME NOT NULL, dt_update DATETIME NOT NULL, user_create INT NOT NULL, user_update INT NOT NULL, content_id INT NOT NULL, section_id INT DEFAULT NULL, active SMALLINT DEFAULT 1 NOT NULL, sort INT DEFAULT 500 NOT NULL, preview_text LONGTEXT DEFAULT NULL, preview_picture INT DEFAULT NULL, detail_text LONGTEXT DEFAULT NULL, detail_picture INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, counter INT DEFAULT 0 NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description LONGTEXT DEFAULT NULL, meta_keywords LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE content_element');
    }
}
