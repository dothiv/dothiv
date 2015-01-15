<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Rename DomainConfigurationNotification table
 */
class Version20150114125555 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('RENAME TABLE DomainConfigurationNotification TO UserReminder');
        $this->addSql('ALTER TABLE UserReminder ADD type VARCHAR(255) NOT NULL AFTER domain_id');
        $this->addSql('UPDATE UserReminder SET type = "configuration"');
        $this->addSql('ALTER TABLE UserReminder ADD ident VARCHAR(255) NOT NULL AFTER domain_id');
        $this->addSql('ALTER TABLE UserReminder ADD INDEX userreminder__ident_idx (ident)');
        $this->addSql('UPDATE UserReminder SET ident = (SELECT name FROM Domain d WHERE d.id = domain_id)');
        $this->addSql('ALTER TABLE UserReminder DROP FOREIGN KEY FK_11406178115F0EE5');
        $this->addSql('ALTER TABLE UserReminder DROP domain_id');
    }

    public function down(Schema $schema)
    {
        $this->addSql('RENAME TABLE UserReminder TO DomainConfigurationNotification');
        $this->addSql('ALTER TABLE DomainConfigurationNotification ADD domain_id INT DEFAULT NULL AFTER id');
        $this->addSql('ALTER TABLE DomainConfigurationNotification ADD INDEX IDX_11406178115F0EE5 (domain_id)');
        $this->addSql('UPDATE DomainConfigurationNotification SET domain_id = (SELECT id FROM Domain d WHERE d.name = ident)');
        $this->addSql('ALTER TABLE DomainConfigurationNotification ADD CONSTRAINT FK_11406178115F0EE5 FOREIGN KEY (domain_id) REFERENCES Domain (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE DomainConfigurationNotification DROP ident');
        $this->addSql('ALTER TABLE DomainConfigurationNotification DROP type');
    }
}
