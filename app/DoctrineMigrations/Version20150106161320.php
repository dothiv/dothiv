<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add click4life order infos to shop order
 */
class Version20150106161320 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE ShopOrder ADD gift TINYINT(1) NOT NULL AFTER redirect, ADD presenteeFirstname VARCHAR(255) DEFAULT NULL AFTER gift, ADD presenteeLastname VARCHAR(255) DEFAULT NULL AFTER presenteeFirstname, ADD presenteeEmail VARCHAR(255) DEFAULT NULL AFTER presenteeLastname, ADD language VARCHAR(255) NOT NULL AFTER presenteeEmail, CHANGE redirect redirect VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE ShopOrder SET language = "en" WHERE language = ""');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE ShopOrder DROP gift, DROP presenteeFirstname, DROP presenteeLastname, DROP presenteeEmail, DROP language, CHANGE redirect redirect VARCHAR(255) NOT NULL');
    }
}
