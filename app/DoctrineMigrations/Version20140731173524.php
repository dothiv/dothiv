<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add Premium Banner configuration table.
 */
class Version20140731173524 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE PremiumBanner (id INT AUTO_INCREMENT NOT NULL, visual_id INT DEFAULT NULL, bg_id INT DEFAULT NULL, banner_id INT DEFAULT NULL, fontColor VARCHAR(255) DEFAULT NULL, bgColor VARCHAR(255) DEFAULT NULL, barColor VARCHAR(255) DEFAULT NULL, headlineFont VARCHAR(255) DEFAULT NULL, headlineFontStyle VARCHAR(255) DEFAULT NULL, textFont VARCHAR(255) DEFAULT NULL, textFontStyle VARCHAR(255) DEFAULT NULL, extrasHeadline VARCHAR(255) DEFAULT NULL, extrasText LONGTEXT DEFAULT NULL, extrasLinkUrl VARCHAR(255) DEFAULT NULL, extrasLinkLabel VARCHAR(255) DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, extrasVisual_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_ECF7427E60D949C1 (visual_id), UNIQUE INDEX UNIQ_ECF7427E1A2100A8 (bg_id), UNIQUE INDEX UNIQ_ECF7427E4565F42A (extrasVisual_id), INDEX IDX_ECF7427E684EC833 (banner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE PremiumBanner ADD CONSTRAINT FK_ECF7427E60D949C1 FOREIGN KEY (visual_id) REFERENCES Attachment (id) ON DELETE RESTRICT");
        $this->addSql("ALTER TABLE PremiumBanner ADD CONSTRAINT FK_ECF7427E1A2100A8 FOREIGN KEY (bg_id) REFERENCES Attachment (id) ON DELETE RESTRICT");
        $this->addSql("ALTER TABLE PremiumBanner ADD CONSTRAINT FK_ECF7427E4565F42A FOREIGN KEY (extrasVisual_id) REFERENCES Attachment (id) ON DELETE RESTRICT");
        $this->addSql("ALTER TABLE PremiumBanner ADD CONSTRAINT FK_ECF7427E684EC833 FOREIGN KEY (banner_id) REFERENCES Banner (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE PremiumBanner");
    }
}
