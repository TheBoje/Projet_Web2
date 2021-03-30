<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210330071323 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "im2021_order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client INTEGER NOT NULL, product INTEGER NOT NULL, quantity INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_1076FD5FC7440455 ON "im2021_order" (client)');
        $this->addSql('CREATE INDEX IDX_1076FD5FD34A04AD ON "im2021_order" (product)');
        $this->addSql('CREATE UNIQUE INDEX prod_user_idx ON "im2021_order" (client, product)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im2021_user AS SELECT id, login, password, name, firstname, birthdate, is_admin FROM im2021_user');
        $this->addSql('DROP TABLE im2021_user');
        $this->addSql('CREATE TABLE im2021_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(255) NOT NULL COLLATE BINARY, password VARCHAR(40) NOT NULL COLLATE BINARY, name VARCHAR(32) NOT NULL COLLATE BINARY, firstname VARCHAR(32) NOT NULL COLLATE BINARY, birthdate DATE NOT NULL, is_admin BOOLEAN DEFAULT \'0\' NOT NULL)');
        $this->addSql('INSERT INTO im2021_user (id, login, password, name, firstname, birthdate, is_admin) SELECT id, login, password, name, firstname, birthdate, is_admin FROM __temp__im2021_user');
        $this->addSql('DROP TABLE __temp__im2021_user');
        $this->addSql('CREATE UNIQUE INDEX log_idx ON im2021_user (login)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "im2021_order"');
        $this->addSql('DROP INDEX log_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im2021_user AS SELECT id, login, password, name, firstname, birthdate, is_admin FROM "im2021_user"');
        $this->addSql('DROP TABLE "im2021_user"');
        $this->addSql('CREATE TABLE "im2021_user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(40) NOT NULL, name VARCHAR(32) NOT NULL, firstname VARCHAR(32) NOT NULL, birthdate DATE NOT NULL, is_admin BOOLEAN DEFAULT \'0\' NOT NULL)');
        $this->addSql('INSERT INTO "im2021_user" (id, login, password, name, firstname, birthdate, is_admin) SELECT id, login, password, name, firstname, birthdate, is_admin FROM __temp__im2021_user');
        $this->addSql('DROP TABLE __temp__im2021_user');
    }
}
