<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Split domain_online_but_click_counter_not_configured into non-profit and for-profit
 */
class Version20150122162528 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO UserReminder (ident, type, created) SELECT d.name, 'domain_online_but_click_counter_not_configured_nonprofit', NOW() FROM Domain d WHERE d.name IN (SELECT ident FROM UserReminder WHERE type = 'domain_online_but_click_counter_not_configured') AND d.nonprofit = 1");
        $this->addSql("INSERT INTO UserReminder (ident, type, created) SELECT d.name, 'domain_online_but_click_counter_not_configured_forprofit', NOW() FROM Domain d WHERE d.name IN (SELECT ident FROM UserReminder WHERE type = 'domain_online_but_click_counter_not_configured') AND d.nonprofit = 0");
        $this->addSql("DELETE FROM UserReminder WHERE type = 'domain_online_but_click_counter_not_configured'");
    }

    public function down(Schema $schema)
    {
        $this->addSql("INSERT INTO UserReminder (ident, type, created) SELECT ident, 'domain_online_but_click_counter_not_configured', created FROM UserReminder WHERE type IN ('domain_online_but_click_counter_not_configured_forprofit', 'domain_online_but_click_counter_not_configured_nonprofit')");
    }
}
