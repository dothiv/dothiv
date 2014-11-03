<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Drop Page entity table 
 */
class Version20141030173132 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('DROP TABLE Page');
    }

    public function down(Schema $schema)
    {
        $this->addSql('CREATE TABLE Page (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(2048) NOT NULL, protocol VARCHAR(2048) NOT NULL, host VARCHAR(2048) NOT NULL, fragment VARCHAR(2048) NOT NULL, locale VARCHAR(16) NOT NULL, dom LONGBLOB NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }
}
