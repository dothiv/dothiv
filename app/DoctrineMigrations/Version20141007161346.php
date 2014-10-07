<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add Payitforward Voucher table
 */
class Version20141007161346 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE Voucher (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, updated DATETIME NOT NULL, created DATETIME NOT NULL, INDEX IDX_DC2F9C448D9F6D38 (order_id), UNIQUE INDEX payitforward_voucher__code (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Voucher ADD CONSTRAINT FK_DC2F9C448D9F6D38 FOREIGN KEY (order_id) REFERENCES PayitforwardOrder (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE Voucher');
    }
}
