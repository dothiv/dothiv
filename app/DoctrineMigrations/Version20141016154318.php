<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Remove table DomainAlternative
 */
class Version20141016154318 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('DROP TABLE DomainAlternative');
    }

    public function down(Schema $schema)
    {
        $this->addSql('CREATE TABLE DomainAlternative (id INT AUTO_INCREMENT NOT NULL, domain VARCHAR(255) NOT NULL, trusted TINYINT(1) NOT NULL, hivDomain_id INT DEFAULT NULL, INDEX IDX_54090CEAF0E21913 (hivDomain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE DomainAlternative ADD CONSTRAINT FK_54090CEAF0E21913 FOREIGN KEY (hivDomain_id) REFERENCES Domain (id)');
    }
}
