<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create a table for user profile changes.
 */
class Version20141110162927 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE UserProfileChange (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, properties LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', userUpdate DATETIME DEFAULT NULL, confirmed TINYINT(1) NOT NULL, sent TINYINT(1) NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_7D5DEFFCA76ED395 (user_id), UNIQUE INDEX user_profile_change__user___token (user_id, token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE UserProfileChange ADD CONSTRAINT FK_7D5DEFFCA76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE UserProfileChange');
    }
}
