<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This migration changes all domain names in the NonProfitRegistration table to be lowercase.
 *
 * @author Markus Tacker <m@tld.hiv>
 */
class Version20140722113127 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE NonProfitRegistration SET domain = LOWER(domain)");
    }

    public function down(Schema $schema)
    {
        // Not downgradeable.
    }
}
