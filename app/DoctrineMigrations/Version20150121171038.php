<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Fix migration 20150120135829 marking ALL domains live
 */
class Version20150121171038 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE Domain d SET live = NULL');
        $this->addSql('UPDATE Domain d SET live = (SELECT IF(c.valid,created,NULL) FROM HivDomainCheck c WHERE c.domain_id = d.id ORDER BY c.id DESC LIMIT 1)');
    }

    public function down(Schema $schema)
    {
    }
}
