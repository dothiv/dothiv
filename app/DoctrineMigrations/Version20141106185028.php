<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add created / updated timestamp to user table
 */
class Version20141106185028 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE User ADD updated DATETIME DEFAULT NULL, ADD created DATETIME NOT NULL');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE User DROP updated, DROP created');
    }
}
