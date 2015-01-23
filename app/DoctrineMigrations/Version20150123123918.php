<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add DomainWhois table
 */
class Version20150123123918 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE DomainWhois (id INT AUTO_INCREMENT NOT NULL, domain VARCHAR(255) NOT NULL, whois LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, UNIQUE INDEX domainwhois__domain (domain), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE DomainWhois');
    }
}
