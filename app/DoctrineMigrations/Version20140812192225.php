<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add missing person position and concept to nonprofit domain registration.
 */
class Version20140812192225 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE NonProfitRegistration ADD personPosition VARCHAR(255) DEFAULT NULL, ADD concept LONGTEXT DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE NonProfitRegistration DROP personPosition, DROP concept");
    }
}
