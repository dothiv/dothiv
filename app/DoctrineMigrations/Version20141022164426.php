<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Rename a config value for the afilias importer.
 */
class Version20141022164426 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE `Config` SET `name` = "dothiv_afilias_importer.registrations.next_url" WHERE `name` = "dothiv_afilias_importer.next_url"');
    }

    public function down(Schema $schema)
    {
        $this->addSql('UPDATE `Config` SET `name` = "dothiv_afilias_importer.next_url" WHERE `name` = "dothiv_afilias_importer.registrations.next_url"');
    }
}
