<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add extra fields for premium subscriptions.
 */
class Version20140826143417 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Subscription ADD type VARCHAR(255) NOT NULL, ADD fullname VARCHAR(255) NOT NULL, ADD address1 VARCHAR(255) NOT NULL, ADD address2 VARCHAR(255) DEFAULT NULL, ADD country VARCHAR(255) NOT NULL, ADD vatNo VARCHAR(255) DEFAULT NULL, ADD taxNo VARCHAR(255) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE Subscription DROP type, DROP fullname, DROP address1, DROP address2, DROP country, DROP vatNo, DROP taxNo");
    }
}
