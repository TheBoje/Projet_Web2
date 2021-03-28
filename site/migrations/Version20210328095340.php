<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210328095340 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "im2021_product" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL, price INTEGER NOT NULL, quantity INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE "im2021_user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(40) NOT NULL, name VARCHAR(32) NOT NULL, firstname VARCHAR(32) NOT NULL, birthdate DATE NOT NULL, is_admin BOOLEAN DEFAULT \'0\' NOT NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "im2021_product"');
        $this->addSql('DROP TABLE "im2021_user"');
    }
}
