<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Creates the table for UserNotifications
 */
class Version20141106163500 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE UserNotification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, properties LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', dismissed TINYINT(1) NOT NULL, updated DATETIME NOT NULL, created DATETIME NOT NULL, INDEX IDX_47FCAD24A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE UserNotification ADD CONSTRAINT FK_47FCAD24A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE UserNotification');
    }
}
