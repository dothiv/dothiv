<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Remove sent flag on UserProfileChange
 */
class Version20141111164337 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE UserProfileChange DROP sent');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE UserProfileChange ADD sent TINYINT(1) NOT NULL');
    }
}
