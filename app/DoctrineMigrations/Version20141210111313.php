<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds the live flag to domains.
 * Adds the table which stores hiv check results
 */
class Version20141210111313 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE HivDomainCheck (id INT AUTO_INCREMENT NOT NULL, domain_id INT NOT NULL, dnsOk TINYINT(1) NOT NULL, addresses LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', url LONGTEXT NOT NULL, statusCode INT NOT NULL, scriptPresent TINYINT(1) NOT NULL, iframePresent TINYINT(1) NOT NULL, iframeTarget LONGTEXT DEFAULT NULL, iframeTargetOk TINYINT(1) NOT NULL, valid TINYINT(1) NOT NULL, created DATETIME NOT NULL, INDEX IDX_1E784F2115F0EE5 (domain_id), INDEX hivdomaincheck__created_idx (created), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE HivDomainCheck ADD CONSTRAINT FK_1E784F2115F0EE5 FOREIGN KEY (domain_id) REFERENCES Domain (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Domain ADD live TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE HivDomainCheck');
        $this->addSql('ALTER TABLE Domain DROP live');
    }
}
