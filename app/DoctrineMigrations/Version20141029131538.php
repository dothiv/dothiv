<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table for tracking entity changes
 */
class Version20141029131538 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE EntityChange (id INT AUTO_INCREMENT NOT NULL, author VARCHAR(255) NOT NULL, entity VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, changes LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', created DATETIME NOT NULL, INDEX entity_change__author_idx (author), INDEX entity_change__entity_idx (entity), INDEX entity_change__identifier_idx (identifier), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE EntityChange');
    }
}
