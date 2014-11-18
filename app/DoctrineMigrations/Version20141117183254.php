<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add DomainCollaborator table.
 */
class Version20141117183254 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE DomainCollaborator (id INT AUTO_INCREMENT NOT NULL, domain_id INT NOT NULL, user_id INT NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_7D80AE49115F0EE5 (domain_id), INDEX IDX_7D80AE49A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE DomainCollaborator ADD CONSTRAINT FK_7D80AE49115F0EE5 FOREIGN KEY (domain_id) REFERENCES Domain (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE DomainCollaborator ADD CONSTRAINT FK_7D80AE49A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE DomainCollaborator');
    }
}
