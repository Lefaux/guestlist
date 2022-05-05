<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220505211503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_ACB79A3571F7E88B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__guest AS SELECT id, event_id, first_name, last_name, pluses, check_in_time, checked_in_pluses FROM guest');
        $this->addSql('DROP TABLE guest');
        $this->addSql('CREATE TABLE guest (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, event_id INTEGER DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, pluses INTEGER DEFAULT NULL, check_in_time DATETIME DEFAULT NULL, checked_in_pluses INTEGER DEFAULT NULL, vip BOOLEAN DEFAULT 0 NOT NULL, CONSTRAINT FK_ACB79A3571F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO guest (id, event_id, first_name, last_name, pluses, check_in_time, checked_in_pluses) SELECT id, event_id, first_name, last_name, pluses, check_in_time, checked_in_pluses FROM __temp__guest');
        $this->addSql('DROP TABLE __temp__guest');
        $this->addSql('CREATE INDEX IDX_ACB79A3571F7E88B ON guest (event_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_ACB79A3571F7E88B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__guest AS SELECT id, event_id, first_name, last_name, pluses, check_in_time, checked_in_pluses FROM guest');
        $this->addSql('DROP TABLE guest');
        $this->addSql('CREATE TABLE guest (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, event_id INTEGER DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, pluses INTEGER DEFAULT NULL, check_in_time DATETIME DEFAULT NULL, checked_in_pluses INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO guest (id, event_id, first_name, last_name, pluses, check_in_time, checked_in_pluses) SELECT id, event_id, first_name, last_name, pluses, check_in_time, checked_in_pluses FROM __temp__guest');
        $this->addSql('DROP TABLE __temp__guest');
        $this->addSql('CREATE INDEX IDX_ACB79A3571F7E88B ON guest (event_id)');
    }
}
