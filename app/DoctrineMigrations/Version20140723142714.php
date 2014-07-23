<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add created and updated time fields to domain.
 *
 * @author Markus Tacker <m@click4life.hiv>
 */
class Version20140723142714 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Domain ADD created DATETIME NOT NULL, ADD updated DATETIME NOT NULL, DROP lastUpdate");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE Domain ADD lastUpdate DATETIME DEFAULT NULL, DROP created, DROP updated");
    }
}
