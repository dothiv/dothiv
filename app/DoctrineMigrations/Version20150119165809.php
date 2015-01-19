<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * 4life.hiv domains: Add field for landingpage owner
 */
class Version20150119165809 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE ShopOrder ADD landingpageOwner VARCHAR(255) DEFAULT NULL AFTER redirect');
        $this->addSql('UPDATE ShopOrder SET landingpageOwner = firstname WHERE presenteeFirstname IS NULL');
        $this->addSql('UPDATE ShopOrder SET landingpageOwner = presenteeFirstname WHERE presenteeFirstname IS NOT NULL');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE ShopOrder DROP landingpageOwner');
    }
}
