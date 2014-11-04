<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add registered and approved flags for NonProfitRegistration
 */
class Version20141104171205 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE NonProfitRegistration ADD approved DATETIME DEFAULT NULL, ADD registered DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE NonProfitRegistration DROP approved, DROP registered');
    }
}
