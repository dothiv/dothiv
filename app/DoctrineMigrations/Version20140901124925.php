<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add created column to UserToken, see https://trello.com/c/H9L8hoAp
 */
class Version20140901124925 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE UserToken ADD scope VARCHAR(255) NOT NULL, ADD created DATETIME NOT NULL");
        $this->addSql("CREATE INDEX usertoken__scope_idex ON UserToken (scope)");
    }

    public function down(Schema $schema)
    {
        $this->addSql("DROP INDEX usertoken__scope_idex ON UserToken");
        $this->addSql("ALTER TABLE UserToken DROP scope, DROP created");
    }
}
