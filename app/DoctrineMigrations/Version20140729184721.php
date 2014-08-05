<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Updates required to support different types of attachments
 */
class Version20140729184721 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Attachment ADD mimeType VARCHAR(255) NOT NULL, ADD extension VARCHAR(255) NOT NULL, ADD created DATETIME NOT NULL, ADD updated DATETIME NOT NULL");
        $this->addSql("UPDATE Attachment a SET mimeType='application/pdf', extension='pdf', created=(SELECT created FROM NonProfitRegistration n WHERE n.proof_id = a.id), updated=(SELECT created FROM NonProfitRegistration n WHERE n.proof_id = a.id)");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE Attachment DROP mimeType, DROP extension, DROP created, DROP updated");
    }
}
