<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * User firstname may be null (but shouldn't of course).
 *
 * See https://trello.com/c/kDA9gRxM
 */
class Version20140909211557 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE User CHANGE firstname firstname VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE User CHANGE firstname firstname VARCHAR(255) NOT NULL');
    }
}
