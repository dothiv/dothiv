<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140804150307 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Attachment DROP public");
        $this->addSql("ALTER TABLE Domain DROP FOREIGN KEY FK_A0051B3DA7A91E0B");
        $this->addSql("ALTER TABLE Domain ADD CONSTRAINT FK_A0051B3DA7A91E0B FOREIGN KEY (domain) REFERENCES Banner (id) ON DELETE SET NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Attachment ADD public INT NOT NULL");
        $this->addSql("ALTER TABLE Domain DROP FOREIGN KEY FK_A0051B3DA7A91E0B");
        $this->addSql("ALTER TABLE Domain ADD CONSTRAINT FK_A0051B3DA7A91E0B FOREIGN KEY (domain) REFERENCES Banner (id) ON DELETE CASCADE");
    }
}
