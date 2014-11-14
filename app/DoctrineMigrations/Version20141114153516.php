<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Dothiv\BaseWebsiteBundle\Cache\RequestLastModifiedCache;

/**
 * Insert a config value for the page cache, if not exists
 */
class Version20141114153516 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $date = new \DateTime();
        $this->addSql("INSERT IGNORE INTO Config (`name`, `value`, `created`) VALUES('" . RequestLastModifiedCache::CONFIG_NAME . "', '"  . $date->format(DATE_W3C) . "', NOW())");
    }

    public function down(Schema $schema)
    {
    }
}
