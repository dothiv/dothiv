<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Change stripe column name on payitforward order table
 */
class Version20141007161848 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE PayitforwardOrder CHANGE customer charge VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE PayitforwardOrder CHANGE charge customer VARCHAR(255) DEFAULT NULL');
    }
}
