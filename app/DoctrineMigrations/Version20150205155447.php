<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add LandingpageConfiguration table
 */
class Version20150205155447 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE LandingpageConfiguration (id INT AUTO_INCREMENT NOT NULL, domain_id INT DEFAULT NULL, clickCounter TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, language VARCHAR(255) NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, UNIQUE INDEX landingpageconfig__domain (domain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE LandingpageConfiguration ADD CONSTRAINT FK_4F614E11115F0EE5 FOREIGN KEY (domain_id) REFERENCES Domain (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO LandingpageConfiguration (domain_id, clickCounter, language, name, created) SELECT d.id, o.clickCounter, o.language, o.landingpageOwner, IF(o.updated > o.created, o.updated, o.created) FROM ShopOrder o LEFT JOIN Domain d ON d.name = o.domain WHERE stripeCharge IS NOT NULL AND domain LIKE "%4life.hiv" AND domain != "4life.hiv" HAVING d.id IS NOT NULL');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE LandingpageConfiguration');
    }
}
