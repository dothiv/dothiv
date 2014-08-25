<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Update Premium Click-Counter font settings.
 */
class Version20140825140644 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE PremiumBanner ADD headlineFontWeight VARCHAR(255) DEFAULT NULL, ADD headlineFontSize INT DEFAULT NULL, ADD textFontWeight VARCHAR(255) DEFAULT NULL, ADD textFontSize INT DEFAULT NULL, DROP headlineFontStyle, DROP textFontStyle");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE PremiumBanner ADD headlineFontStyle VARCHAR(255) DEFAULT NULL, ADD textFontStyle VARCHAR(255) DEFAULT NULL, DROP headlineFontWeight, DROP headlineFontSize, DROP textFontWeight, DROP textFontSize");
    }
}
