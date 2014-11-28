<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add config values for start page tiles
 */
class Version20141120164700 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO `Config` (`name`, `value`) VALUES ("hivdomain.min_price", "142.64")');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DELETE FROM `Config` WHERE `name` = "hivdomain.min_price"');
    }
}
