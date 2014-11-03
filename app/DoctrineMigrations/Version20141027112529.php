<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Convert parameter "eur_to_usd" to config value.
 */
class Version20141027112529 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO `Config` (`name`, `value`) VALUES ("eur_to_usd", "1.268475")');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DELETE FROM `Config` WHERE `name` = "eur_to_usd"');
    }
}
