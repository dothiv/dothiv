<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migrations for premium clickcounter subscriptions.
 */
class Version20140805164019 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE Subscription (id INT AUTO_INCREMENT NOT NULL, domain_id INT NOT NULL, user_id INT NOT NULL, email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, customer VARCHAR(255) DEFAULT NULL, liveMode INT NOT NULL, active INT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_BBF7BF2B115F0EE5 (domain_id), INDEX IDX_BBF7BF2BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE Subscription ADD CONSTRAINT FK_BBF7BF2B115F0EE5 FOREIGN KEY (domain_id) REFERENCES Domain (id) ON DELETE RESTRICT");
        $this->addSql("ALTER TABLE Subscription ADD CONSTRAINT FK_BBF7BF2BA76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE RESTRICT");
    }

    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE Subscription");
    }
}
