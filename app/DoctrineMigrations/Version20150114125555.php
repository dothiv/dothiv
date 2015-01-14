<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Rename DomainConfiguratioNotification table
 */
class Version20150114125555 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('RENAME TABLE DomainConfigurationNotification TO DomainNotification');
        $this->addSql('ALTER TABLE DomainNotification ADD type VARCHAR(255) NOT NULL AFTER domain_id');
        $this->addSql('UPDATE DomainNotification SET type = "configuration"');
    }

    public function down(Schema $schema)
    {
        $this->addSql('RENAME TABLE DomainNotification TO DomainConfigurationNotification');
        $this->addSql('ALTER TABLE DomainConfigurationNotification DROP type');
    }
}
