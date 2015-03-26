<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Fixes organization name in shop order invoices
 */
class Version20150326112056 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE `ShopOrder` o SET o.`organization` = NULL, o.`updated` = NOW() WHERE o.`organization` = ''");

        $this->addSql("UPDATE `Invoice` i SET i.`organization` = (SELECT o.`organization` FROM `ShopOrder` o WHERE o.`invoice_id` = i.`id`), i.`updated` = NOW() WHERE i.`organization` IS NULL AND i.id IN (SELECT o.invoice_id FROM `ShopOrder` o WHERE o.`invoice_id` IS NOT NULL AND o.`organization` IS NOT NULL)");
    }

    public function down(Schema $schema)
    {
    }
}
