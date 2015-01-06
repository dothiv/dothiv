<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add currency to invoices.
 * Add invoice to shop orders.
 */
class Version20150106184133 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Invoice ADD currency VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE Invoice SET currency = "EUR"');
        $this->addSql('ALTER TABLE ShopOrder ADD invoice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ShopOrder ADD CONSTRAINT FK_2ACEA85F2989F1FD FOREIGN KEY (invoice_id) REFERENCES Invoice (id)');
        $this->addSql('CREATE INDEX IDX_2ACEA85F2989F1FD ON ShopOrder (invoice_id)');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE Invoice DROP currency');
        $this->addSql('ALTER TABLE ShopOrder DROP FOREIGN KEY FK_2ACEA85F2989F1FD');
        $this->addSql('DROP INDEX IDX_2ACEA85F2989F1FD ON ShopOrder');
        $this->addSql('ALTER TABLE ShopOrder DROP invoice_id');
    }
}
