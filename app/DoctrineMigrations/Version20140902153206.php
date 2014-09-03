<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Dothiv\BusinessBundle\Entity\Registrar;

/**
 * Add entity for registrars.
 */
class Version20140902153206 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE Registrar (id INT AUTO_INCREMENT NOT NULL, extId VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, registrationNotification VARCHAR(255) NOT NULL, updated DATETIME NOT NULL, created DATETIME NOT NULL, UNIQUE INDEX domain__extId (extId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('INSERT INTO Registrar (extId, name, registrationNotification, updated, created) VALUES ("1234-AA", "Default Registrar", "' . Registrar::REGISTRATION_NOFITICATION_REGULAR . '", NOW(), NOW())');
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');
        $this->addSql('ALTER TABLE Domain ADD registrar_id INT NOT NULL');
        $this->addSql('UPDATE Domain SET registrar_id = (SELECT id FROM Registrar WHERE extId = "1234-AA")');
        $this->addSql('ALTER TABLE Domain ADD CONSTRAINT FK_A0051B3DD1AA2FC1 FOREIGN KEY (registrar_id) REFERENCES Registrar (id)');
        $this->addSql('CREATE INDEX IDX_A0051B3DD1AA2FC1 ON Domain (registrar_id)');
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE Domain DROP FOREIGN KEY FK_A0051B3DD1AA2FC1');
        $this->addSql('DROP TABLE Registrar');
        $this->addSql('DROP INDEX IDX_A0051B3DD1AA2FC1 ON Domain');
        $this->addSql('ALTER TABLE Domain DROP registrar_id');
    }
}
