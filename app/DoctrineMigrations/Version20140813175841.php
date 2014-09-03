<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add tables for storing premium name bids.
 */
class Version20140813175841 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE PremiumBid (id INT AUTO_INCREMENT NOT NULL, domain VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, notified DATETIME DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
    }

    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE PremiumBid");
    }
}
