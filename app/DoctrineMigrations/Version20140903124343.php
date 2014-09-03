<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add created / updated to banner
 */
class Version20140903124343 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Banner ADD updated DATETIME NOT NULL, ADD created DATETIME NOT NULL');
        $this->addSql('UPDATE Banner SET updated=NOW(), created=NOW()');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE Banner DROP updated, DROP created');
    }
}
