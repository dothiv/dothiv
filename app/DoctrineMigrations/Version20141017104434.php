<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create DomainConfigurationNotification table
 */
class Version20141017104434 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE DomainConfigurationNotification (id INT AUTO_INCREMENT NOT NULL, domain_id INT DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_11406178115F0EE5 (domain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE DomainConfigurationNotification ADD CONSTRAINT FK_11406178115F0EE5 FOREIGN KEY (domain_id) REFERENCES Domain (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE DomainConfigurationNotification');
    }
}
