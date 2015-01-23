<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Fix for ShopOrders fields beeing filled with empty strings
 */
class Version20150123160033 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        foreach (['fax', 'locality2', 'vatNo', 'presenteeFirstname', 'presenteeLastname', 'landingpageOwner'] as $field) {
            $this->addSql(sprintf('UPDATE ShopOrder SET %s = NULL where %s = ""', $field, $field));
        }
    }

    public function down(Schema $schema)
    {
    }
}
