<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add payitforward order table.
 */
class Version20141007111336 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE payitforward_order (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, firstname VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, domain VARCHAR(255) DEFAULT NULL, domainDonor VARCHAR(255) DEFAULT NULL, domainDonorTwitter VARCHAR(255) DEFAULT NULL, token VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, fullname VARCHAR(255) NOT NULL, address1 VARCHAR(255) NOT NULL, address2 VARCHAR(255) DEFAULT NULL, country VARCHAR(255) NOT NULL, vatNo VARCHAR(255) DEFAULT NULL, taxNo VARCHAR(255) DEFAULT NULL, domain1 VARCHAR(255) DEFAULT NULL, domain1Name VARCHAR(255) DEFAULT NULL, domain1Company VARCHAR(255) DEFAULT NULL, domain1Twitter VARCHAR(255) DEFAULT NULL, domain2 VARCHAR(255) DEFAULT NULL, domain2Name VARCHAR(255) DEFAULT NULL, domain2Company VARCHAR(255) DEFAULT NULL, domain2Twitter VARCHAR(255) DEFAULT NULL, domain3 VARCHAR(255) DEFAULT NULL, domain3Name VARCHAR(255) DEFAULT NULL, domain3Company VARCHAR(255) DEFAULT NULL, domain3Twitter VARCHAR(255) DEFAULT NULL, customer VARCHAR(255) DEFAULT NULL, liveMode INT NOT NULL, created DATETIME NOT NULL, INDEX IDX_CDEE222FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payitforward_order ADD CONSTRAINT FK_CDEE222FA76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE payitforward_order');
    }
}
