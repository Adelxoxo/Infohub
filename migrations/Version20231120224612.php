<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231120224612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, name, long_text, banner, crdate, last_modified FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, long_text CLOB DEFAULT NULL, banner VARCHAR(255) DEFAULT NULL, crdate DATETIME DEFAULT NULL, last_modified DATETIME DEFAULT NULL, CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO post (id, name, long_text, banner, crdate, last_modified) SELECT id, name, long_text, banner, crdate, last_modified FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D12469DE2 ON post (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, name, long_text, banner, crdate, last_modified FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, long_text CLOB DEFAULT NULL, banner VARCHAR(255) DEFAULT NULL, crdate DATETIME DEFAULT NULL, last_modified DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO post (id, name, long_text, banner, crdate, last_modified) SELECT id, name, long_text, banner, crdate, last_modified FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
    }
}
