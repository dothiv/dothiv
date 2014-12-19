<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create Tables for .hiv Shop
 */
class Version20141218223037 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE ShopOrder (id INT AUTO_INCREMENT NOT NULL, domain VARCHAR(255) NOT NULL, clickCounter TINYINT(1) NOT NULL, redirect VARCHAR(255) NOT NULL, duration INT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, fax VARCHAR(255) DEFAULT NULL, locality VARCHAR(255) NOT NULL, locality2 VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, organization VARCHAR(255) DEFAULT NULL, vatNo VARCHAR(255) DEFAULT NULL, currency VARCHAR(255) NOT NULL, stripeCard VARCHAR(255) NOT NULL, stripeToken VARCHAR(255) NOT NULL, stripeCharge VARCHAR(255) DEFAULT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE DomainInfo (name VARCHAR(255) NOT NULL, registered TINYINT(1) NOT NULL, premium TINYINT(1) NOT NULL, trademark TINYINT(1) NOT NULL, blocked TINYINT(1) NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('INSERT INTO DomainInfo (name, registered, created) SELECT name, 1, NOW() FROM Domain ON DUPLICATE KEY UPDATE registered = 1, updated=NOW()');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE ShopOrder');
        $this->addSql('DROP TABLE DomainInfo');
    }
}
