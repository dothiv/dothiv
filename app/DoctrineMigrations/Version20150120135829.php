<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Convert domain.live to timestamp
 */
class Version20150120135829 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Domain CHANGE live live DATETIME DEFAULT NULL');
        $this->addSql('UPDATE Domain d SET live = NULL');
        $this->addSql('UPDATE Domain d SET live = (SELECT created FROM HivDomainCheck c WHERE c.domain_id = d.id GROUP BY domain_id ORDER BY c.id)');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE Domain CHANGE live live TINYINT(1) NOT NULL');
        $this->addSql('UPDATE Domain d SET live = 0');
        $this->addSql('UPDATE Domain d SET live = (SELECT 1 FROM HivDomainCheck c WHERE c.domain_id = d.id GROUP BY domain_id ORDER BY c.id)');
    }
}
