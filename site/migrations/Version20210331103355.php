<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210331103355 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_1076FD5FC7440455');
        $this->addSql('DROP INDEX IDX_1076FD5FD34A04AD');
        $this->addSql('DROP INDEX prod_user_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im2021_order AS SELECT id, client, product, quantity FROM im2021_order');
        $this->addSql('DROP TABLE im2021_order');
        $this->addSql('CREATE TABLE im2021_order (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client INTEGER NOT NULL, product INTEGER NOT NULL, quantity INTEGER NOT NULL, CONSTRAINT FK_1076FD5FC7440455 FOREIGN KEY (client) REFERENCES "im2021_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1076FD5FD34A04AD FOREIGN KEY (product) REFERENCES "im2021_product" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO im2021_order (id, client, product, quantity) SELECT id, client, product, quantity FROM __temp__im2021_order');
        $this->addSql('DROP TABLE __temp__im2021_order');
        $this->addSql('CREATE INDEX IDX_1076FD5FC7440455 ON im2021_order (client)');
        $this->addSql('CREATE INDEX IDX_1076FD5FD34A04AD ON im2021_order (product)');
        $this->addSql('CREATE UNIQUE INDEX prod_user_idx ON im2021_order (client, product)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im2021_product AS SELECT id, label, quantity, price FROM im2021_product');
        $this->addSql('DROP TABLE im2021_product');
        $this->addSql('CREATE TABLE im2021_product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL COLLATE BINARY, quantity INTEGER NOT NULL, price NUMERIC(10, 0) NOT NULL)');
        $this->addSql('INSERT INTO im2021_product (id, label, quantity, price) SELECT id, label, quantity, price FROM __temp__im2021_product');
        $this->addSql('DROP TABLE __temp__im2021_product');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_1076FD5FC7440455');
        $this->addSql('DROP INDEX IDX_1076FD5FD34A04AD');
        $this->addSql('DROP INDEX prod_user_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im2021_order AS SELECT id, client, product, quantity FROM "im2021_order"');
        $this->addSql('DROP TABLE "im2021_order"');
        $this->addSql('CREATE TABLE "im2021_order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client INTEGER NOT NULL, product INTEGER NOT NULL, quantity INTEGER NOT NULL)');
        $this->addSql('INSERT INTO "im2021_order" (id, client, product, quantity) SELECT id, client, product, quantity FROM __temp__im2021_order');
        $this->addSql('DROP TABLE __temp__im2021_order');
        $this->addSql('CREATE INDEX IDX_1076FD5FC7440455 ON "im2021_order" (client)');
        $this->addSql('CREATE INDEX IDX_1076FD5FD34A04AD ON "im2021_order" (product)');
        $this->addSql('CREATE UNIQUE INDEX prod_user_idx ON "im2021_order" (client, product)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im2021_product AS SELECT id, label, price, quantity FROM "im2021_product"');
        $this->addSql('DROP TABLE "im2021_product"');
        $this->addSql('CREATE TABLE "im2021_product" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL)');
        $this->addSql('INSERT INTO "im2021_product" (id, label, price, quantity) SELECT id, label, price, quantity FROM __temp__im2021_product');
        $this->addSql('DROP TABLE __temp__im2021_product');
    }
}
