<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table to store invoices.
 */
class Version20141001152938 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE Invoice (id INT AUTO_INCREMENT NOT NULL, fullname VARCHAR(255) NOT NULL, address1 VARCHAR(255) NOT NULL, address2 VARCHAR(255) DEFAULT NULL, country VARCHAR(255) NOT NULL, vatNo VARCHAR(255) DEFAULT NULL, itemDescription VARCHAR(255) NOT NULL, itemPrice INT NOT NULL, vatPrice INT NOT NULL, vatPercent INT NOT NULL, totalPrice INT NOT NULL, updated DATETIME NOT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE Invoice');
    }
}
