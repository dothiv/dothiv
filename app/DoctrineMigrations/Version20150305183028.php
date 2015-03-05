<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This updates existing invoices for shop orders and adds the second address line and the city.
 */
class Version20150305183028 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Invoice i SET address1 = (SELECT CONCAT_WS('\n', o.locality, o.locality2) FROM ShopOrder o WHERE o.invoice_id = i.id), updated = NOW() WHERE i.id = (SELECT o2.invoice_id FROM ShopOrder o2 WHERE o2.invoice_id = i.id)");
        $this->addSql("UPDATE Invoice i SET address2 = (SELECT o.city FROM ShopOrder o WHERE o.invoice_id = i.id), updated = NOW() WHERE i.id = (SELECT o2.invoice_id FROM ShopOrder o2 WHERE o2.invoice_id = i.id)");
    }

    public function down(Schema $schema)
    {
    }
}
